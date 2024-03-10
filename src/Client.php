<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient;

use ArrayAccess\RdapClient\Exceptions\EmptyArgumentException;
use ArrayAccess\RdapClient\Exceptions\InvalidServiceDefinitionException;
use ArrayAccess\RdapClient\Exceptions\UnsupportedProtocolException;
use ArrayAccess\RdapClient\Interfaces\RdapClientInterface;
use ArrayAccess\RdapClient\Interfaces\RdapProtocolInterface;
use ArrayAccess\RdapClient\Protocols\AsnProtocol;
use ArrayAccess\RdapClient\Protocols\DomainProtocol;
use ArrayAccess\RdapClient\Protocols\IPv4Protocol;
use ArrayAccess\RdapClient\Protocols\IPv6Protocol;
use ArrayAccess\RdapClient\Protocols\NsProtocol;
use ArrayAccess\RdapClient\Services\AsnService;
use ArrayAccess\RdapClient\Util\CIDR;
use function explode;
use function idn_to_ascii;
use function is_a;
use function is_array;
use function is_int;
use function is_object;
use function preg_match;
use function sprintf;
use function str_contains;
use function strlen;
use function strtolower;
use function trim;

class Client implements RdapClientInterface
{
    const VERSION = '1.0.0';

    final const PROTOCOLS = [
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

    public function hasProtocol(string $protocolType) : bool
    {
        return isset($this->protocols[$protocolType]);
    }

    public function setProtocol(RdapProtocolInterface $protocol): void
    {
        foreach (self::PROTOCOLS as $protocolVersion => $obj) {
            if (is_a($protocol, $obj)) {
                $this->protocols[$protocolVersion] = $obj;
                break;
            }
        }
        throw new InvalidServiceDefinitionException(
            sprintf(
                'Service protocol "%s" is not supported',
                $protocol::class
            )
        );
    }

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

    public function domain(string $target): ?Interfaces\RdapRequestInterface
    {
        return $this->request($target, self::DOMAIN);
    }

    public function asn(string|int $target): ?Interfaces\RdapRequestInterface
    {
        return $this->request($target, self::ASN);
    }

    public function ipv4(string $target): ?Interfaces\RdapRequestInterface
    {
        return $this->request($target, self::IPV4);
    }

    public function ipv6(string $target): ?Interfaces\RdapRequestInterface
    {
        return $this->request($target, self::IPV6);
    }

    public function nameserver(string $target): ?Interfaces\RdapRequestInterface
    {
        return $this->request($target, self::NS);
    }
}
