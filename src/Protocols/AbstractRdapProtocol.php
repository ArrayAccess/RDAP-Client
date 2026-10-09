<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Protocols;

use ArrayAccess\RdapClient\Exceptions\InvalidServiceDefinitionException;
use ArrayAccess\RdapClient\Exceptions\RdapServerNotFoundException;
use ArrayAccess\RdapClient\Interfaces\RdapClientInterface;
use ArrayAccess\RdapClient\Interfaces\RdapHttpClientAwareInterface;
use ArrayAccess\RdapClient\Interfaces\RdapProtocolInterface;
use ArrayAccess\RdapClient\Interfaces\RdapRequestInterface;
use ArrayAccess\RdapClient\Interfaces\RdapServiceInterface;
use ArrayAccess\RdapClient\Services\AbstractRdapService;
use function explode;
use function is_string;
use function md5;
use function sprintf;
use function str_contains;

abstract class AbstractRdapProtocol implements RdapProtocolInterface
{
    /**
     * @var string $name The name of the protocol
     */
    protected string $name;

    /**
     * @var RdapServiceInterface $services The RDAP service
     */
    protected RdapServiceInterface $services;

    /**
     * @inheritDoc
     */
    public function __construct(protected RdapClientInterface $client)
    {
    }

    /**
     * @inheritDoc
     */
    public function getClient(): RdapClientInterface
    {
        return $this->client;
    }

    /**
     * Load the service from an IANA bootstrap URL
     *
     * Uses the PSR-18 HTTP client and PSR-16 cache of the client when configured,
     * otherwise falls back to {@see AbstractRdapService::fromURL()}.
     *
     * @template T of AbstractRdapService
     * @param class-string<T> $serviceClass
     * @param string $url
     * @return T
     * @throws \Exception
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    protected function loadService(string $serviceClass, string $url): AbstractRdapService
    {
        $client = $this->getClient();
        if (!$client instanceof RdapHttpClientAwareInterface
            || ($client->getHttpClient() === null && $client->getCache() === null)
        ) {
            return $serviceClass::fromURL($url);
        }

        $cache = $client->getCache();
        $cacheKey = 'rdap-client.bootstrap.' . md5($url);
        $content = $cache?->get($cacheKey);
        if (is_string($content)) {
            try {
                return $serviceClass::fromJson($content, $url);
            } catch (InvalidServiceDefinitionException) {
                $cache?->delete($cacheKey);
            }
        }

        $content = $client->fetch($url);
        $service = $serviceClass::fromJson($content, $url);
        $cache?->set($cacheKey, $content, $serviceClass::cacheExpirations());
        return $service;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getService(): RdapServiceInterface
    {
        return $this->services;
    }

    /**
     * @inheritDoc
     */
    public function getFindURL(string $target): string
    {
        $normalize = $this->getService()->normalize($target);
        if (!$normalize) {
            $this->getService()->throwInvalidTarget($target);
        }
        $url = $this->getService()->getRdapURL($normalize);
        if (!$url) {
            throw new RdapServerNotFoundException(
                sprintf('Could not get Rdap URL for %s', $target)
            );
        }

        if (str_contains($url, '#')) {
            [$url] = explode('#', $url);
        }
        if (str_contains($url, '?')) {
            [$url] = explode('?', $url);
        }
        $path = trim($this->getSearchPath(), '/');
        return rtrim($url, '/') . "/$path/$normalize";
    }

    /**
     * @inheritDoc
     */
    public function find(string $target): ?RdapRequestInterface
    {
        return new RdapRequestProtocol($target, $this);
    }
}
