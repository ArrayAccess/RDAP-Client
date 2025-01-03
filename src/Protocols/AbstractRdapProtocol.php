<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Protocols;

use ArrayAccess\RdapClient\Exceptions\RdapServerNotFoundException;
use ArrayAccess\RdapClient\Interfaces\RdapClientInterface;
use ArrayAccess\RdapClient\Interfaces\RdapProtocolInterface;
use ArrayAccess\RdapClient\Interfaces\RdapRequestInterface;
use ArrayAccess\RdapClient\Interfaces\RdapServiceInterface;
use function explode;
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
            [$url] = explode('?', $url);
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
