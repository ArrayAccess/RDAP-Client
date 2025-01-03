<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Protocols;

use ArrayAccess\RdapClient\Exceptions\MismatchProtocolBehaviorException;
use ArrayAccess\RdapClient\Interfaces\RdapClientInterface;
use ArrayAccess\RdapClient\Interfaces\RdapRequestInterface;
use ArrayAccess\RdapClient\Interfaces\RdapResponseInterface;
use ArrayAccess\RdapClient\Response\NsResponse;
use ArrayAccess\RdapClient\Services\NsService;
use function get_class;
use function sprintf;

class NsProtocol extends AbstractRdapProtocol
{
    /**
     * Constructor
     * @param RdapClientInterface $client
     * @param NsService|null $service
     */
    public function __construct(RdapClientInterface $client, ?NsService $service = null)
    {
        parent::__construct($client);
        if ($service) {
            $this->services = $service;
        }
    }

    /**
     * Set service
     *
     * @param NsService $service
     * @return void
     */
    public function setService(NsService $service): void
    {
        $this->services = $service;
    }

    /**
     * @return NsService
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @throws \Exception
     */
    public function getService(): NsService
    {
        if (!isset($this->services) || !($this->services instanceof NsService)) {
            $this->services = NsService::fromURL(self::NS_URI);
        }
        return $this->services;
    }

    /**
     * @inheritDoc
     * @return string
     * @link https://datatracker.ietf.org/doc/html/rfc7482#section-3.1.4
     */
    public function getSearchPath(): string
    {
        return '/nameserver';
    }

    /**
     * @inheritDoc
     */
    public function createResponse(string $response, RdapRequestInterface $request): RdapResponseInterface
    {
        if ($request->getProtocol() !== $this) {
            throw new MismatchProtocolBehaviorException(
                sprintf(
                    'Protocol object "%s" from request is mismatch with protocol object "%s"',
                    get_class($request->getProtocol()),
                    $this::class
                )
            );
        }

        return new NsResponse($response, $request, $this);
    }
}
