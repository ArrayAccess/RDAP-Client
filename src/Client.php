<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient;

use ArrayAccess\RdapClient\Exceptions\EmptyArgumentException;
use ArrayAccess\RdapClient\Exceptions\InvalidServiceDefinitionException;
use ArrayAccess\RdapClient\Exceptions\RdapRemoteRequestException;
use ArrayAccess\RdapClient\Exceptions\UnsupportedProtocolException;
use ArrayAccess\RdapClient\Interfaces\RdapClientInterface;
use ArrayAccess\RdapClient\Interfaces\RdapHttpClientAwareInterface;
use ArrayAccess\RdapClient\Interfaces\RdapProtocolInterface;
use ArrayAccess\RdapClient\Interfaces\RdapRequestInterface;
use ArrayAccess\RdapClient\Protocols\AsnProtocol;
use ArrayAccess\RdapClient\Protocols\DomainProtocol;
use ArrayAccess\RdapClient\Protocols\IPv4Protocol;
use ArrayAccess\RdapClient\Protocols\IPv6Protocol;
use ArrayAccess\RdapClient\Protocols\NsProtocol;
use ArrayAccess\RdapClient\Services\AsnService;
use ArrayAccess\RdapClient\Util\CIDR;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\SimpleCache\CacheInterface;
use function explode;
use function file_get_contents;
use function idn_to_ascii;
use function is_a;
use function is_array;
use function is_int;
use function is_object;
use function is_string;
use function preg_match;
use function restore_error_handler;
use function set_error_handler;
use function sprintf;
use function str_contains;
use function stream_context_create;
use function strlen;
use function strtolower;
use function trim;

class Client implements RdapClientInterface, RdapHttpClientAwareInterface
{
    /**
     * @var string Version
     */
    public const VERSION = '1.0.1';

    /**
     * @var array<string, class-string<RdapProtocolInterface>>
     */
    final public const PROTOCOLS = [
        self::IPV4 => IPv4Protocol::class,
        self::IPV6 => IPv6Protocol::class,
        self::ASN => AsnProtocol::class,
        self::DOMAIN => DomainProtocol::class,
        self::NS => NsProtocol::class,
    ];

    /**
     * @var array<class-string<RdapProtocolInterface>|RdapProtocolInterface>
     */
    protected array $protocols = self::PROTOCOLS;

    /**
     * @var RequestFactoryInterface|null $requestFactory The PSR-17 request factory
     */
    protected ?RequestFactoryInterface $requestFactory;

    /**
     * Constructor
     *
     * Without arguments, requests are made with file_get_contents() and the
     * IANA bootstrap files are cached in the system temporary directory.
     *
     * @param ClientInterface|null $httpClient PSR-18 client used for bootstrap and RDAP requests
     * @param RequestFactoryInterface|null $requestFactory PSR-17 request factory,
     *      optional when the HTTP client also implements RequestFactoryInterface
     * @param CacheInterface|null $cache PSR-16 cache for the IANA bootstrap files
     * @throws InvalidArgumentException if an HTTP client is given without a request factory
     */
    public function __construct(
        protected ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        protected ?CacheInterface $cache = null
    ) {
        if ($requestFactory === null && $httpClient instanceof RequestFactoryInterface) {
            $requestFactory = $httpClient;
        }
        if ($httpClient !== null && $requestFactory === null) {
            throw new InvalidArgumentException(
                'A PSR-17 request factory is required when using a PSR-18 HTTP client'
            );
        }
        $this->requestFactory = $requestFactory;
    }

    /**
     * @inheritDoc
     */
    public function getHttpClient() : ?ClientInterface
    {
        return $this->httpClient;
    }

    /**
     * Get the PSR-17 request factory
     *
     * @return RequestFactoryInterface|null
     */
    public function getRequestFactory() : ?RequestFactoryInterface
    {
        return $this->requestFactory;
    }

    /**
     * @inheritDoc
     */
    public function getCache() : ?CacheInterface
    {
        return $this->cache;
    }

    /**
     * @inheritDoc
     */
    public function fetch(string $url) : string
    {
        if ($this->httpClient === null || $this->requestFactory === null) {
            return $this->fetchWithStream($url);
        }

        $request = $this->requestFactory
            ->createRequest('GET', $url)
            ->withHeader('Accept', 'application/rdap+json,application/json;q=0.9,*/*;q=0.8')
            ->withHeader('User-Agent', 'Rdap-Client/' . self::VERSION);
        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new RdapRemoteRequestException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }

        $content = (string) $response->getBody();
        if ($content === '') {
            throw new RdapRemoteRequestException(
                sprintf('Could not get RDAP response from %s', $url),
                $response->getStatusCode()
            );
        }
        return $content;
    }

    /**
     * Fetch the body of the given URL with file_get_contents()
     *
     * @param string $url
     * @return string
     * @throws RdapRemoteRequestException
     */
    protected function fetchWithStream(string $url) : string
    {
        $errorCode = 0;
        $errorMessage = null;
        set_error_handler(
            static function (int $code, string $message) use (&$errorCode, &$errorMessage) : bool {
                $errorCode = $code;
                $errorMessage = $message;
                return true;
            }
        );
        $content = file_get_contents(
            $url,
            false,
            stream_context_create(RdapRequestInterface::DEFAULT_STREAM_CONTEXT)
        );
        restore_error_handler();
        if (!is_string($content) || $content === '') {
            throw new RdapRemoteRequestException(
                $errorMessage ?? sprintf('Could not get RDAP response from %s', $url),
                $errorCode
            );
        }
        return $content;
    }

    /**
     * Check if protocol is supported
     *
     * @param string $protocolType
     * @return bool
     */
    public function hasProtocol(string $protocolType) : bool
    {
        return isset($this->protocols[$protocolType]);
    }

    /**
     * Set Protocol
     *
     * @param RdapProtocolInterface $protocol
     * @return void
     * @throws InvalidServiceDefinitionException if protocol is not supported
     */
    public function setProtocol(RdapProtocolInterface $protocol): void
    {
        foreach (self::PROTOCOLS as $protocolVersion => $obj) {
            if (is_a($protocol, $obj)) {
                $this->protocols[$protocolVersion] = $protocol;
                return;
            }
        }
        throw new InvalidServiceDefinitionException(
            sprintf(
                'Service protocol "%s" is not supported',
                $protocol::class
            )
        );
    }

    /**
     * Get Protocol
     *
     * @param string $protocolType
     * @return RdapProtocolInterface
     * @throws UnsupportedProtocolException if protocol is not supported
     */
    public function getProtocol(string $protocolType) : RdapProtocolInterface
    {
        if (!isset($this->protocols[$protocolType])
            && isset($this->protocols[strtolower(trim($protocolType))])
        ) {
            $protocolType = strtolower(trim($protocolType));
        }
        if (isset($this->protocols[$protocolType])) {
            if (!is_object($this->protocols[$protocolType])) {
                $this->protocols[$protocolType] = new $this->protocols[$protocolType]($this);
            }
            return $this->protocols[$protocolType];
        }
        throw new UnsupportedProtocolException($protocolType);
    }

    /**
     * Guess type of target
     *
     * @param string $target
     * @return ?array{0:string, 1:string}
     */
    public function guessType(string $target): ?array
    {
        $target = trim($target);
        if (!$target) {
            return null;
        }
        if (str_contains($target, '/') && ($cidr = CIDR::cidrToRange($target))) {
            if (str_contains($cidr[0], ':') || str_contains($cidr[1], ':')) {
                if (CIDR::filterIp6($cidr[0]) && CIDR::filterIp6($cidr[1])) {
                    return [self::IPV6, $target];
                }
            }
            if (str_contains($cidr[0], '.') || str_contains($cidr[1], '.')) {
                if (CIDR::filterIp4($cidr[0]) && CIDR::filterIp4($cidr[1])) {
                    return [self::IPV4, $target];
                }
            }
        }
        if (preg_match('~^(?:ASN?)?([0-9]+)$~i', $target, $match)) {
            $target = $match[1] > 0 && $match[1] <= AsnService::MAX_INTEGER
                ? $match[1]
                : null;
            return $target ? [self::ASN, $target] : null;
        }
        if (str_contains($target, ':') && ($ip6 = CIDR::filterIp6($target))) {
            return [self::IPV6, $ip6];
        }
        if (!str_contains($target, '.')) {
            return null;
        }
        if ($ip4 = CIDR::filterIp4($target)) {
            return [self::IPV4, $ip4];
        }
        $target = idn_to_ascii($target)?:null;
        if (!$target) {
            return null;
        }
        if (strlen($target) > 255) {
            return null;
        }
        foreach (explode('.', $target) as $part) {
            if ($part === '' || strlen($part) > 63) {
                return null;
            }
        }

        // just to try to get nameserver if domains started with ns[0-9]* or name.ns[0-9]*
        if (preg_match('~^((?:[^.]+\.)?ns[0-9]*)\.[^.]+\.~', $target)) {
            return [self::NS, $target];
        }

        return [self::DOMAIN, $target];
    }

    /**
     * Get RDAP Request
     *
     * @param string|int $target
     * @param string|null $protocol
     * @return Interfaces\RdapRequestInterface|null
     */
    public function request(string|int $target, ?string $protocol = null): ?Interfaces\RdapRequestInterface
    {
        if (is_int($target)) {
            $target = (string) $target;
            return $this->getProtocol(self::ASN)->find($target);
        }

        $target = trim($target);
        if ($target === '') {
            throw new EmptyArgumentException(
                'Argument target could not be empty'
            );
        }
        if ($protocol === null) {
            $definitions = $this->guessType($target);
            if (is_array($definitions)) {
                [$protocol, $target] = $definitions;
            }
        }

        if (!$protocol) {
            throw new EmptyArgumentException(
                'Protocol is empty & can not guess.'
            );
        }

        $object = $this->getProtocol($protocol);
        return $object->find($target);
    }

    /**
     * Get Domain Request
     *
     * @param string $target
     * @return Interfaces\RdapRequestInterface|null
     */
    public function domain(string $target): ?Interfaces\RdapRequestInterface
    {
        return $this->request($target, self::DOMAIN);
    }

    /**
     * Get ASN Request
     *
     * @param string|int $target
     * @return Interfaces\RdapRequestInterface|null
     */
    public function asn(string|int $target): ?Interfaces\RdapRequestInterface
    {
        return $this->request($target, self::ASN);
    }

    /**
     * Get ipv4 Request
     *
     * @param string $target
     * @return Interfaces\RdapRequestInterface|null
     */
    public function ipv4(string $target): ?Interfaces\RdapRequestInterface
    {
        return $this->request($target, self::IPV4);
    }

    /**
     * Get IPv6 Request
     *
     * @param string $target
     * @return Interfaces\RdapRequestInterface|null
     */
    public function ipv6(string $target): ?Interfaces\RdapRequestInterface
    {
        return $this->request($target, self::IPV6);
    }

    /**
     * Get nameserver Request
     *
     * @param string $target
     * @return Interfaces\RdapRequestInterface|null
     */
    public function nameserver(string $target): ?Interfaces\RdapRequestInterface
    {
        return $this->request($target, self::NS);
    }
}
