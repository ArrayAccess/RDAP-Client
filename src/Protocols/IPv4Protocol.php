<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Protocols;

use ArrayAccess\RdapClient\Exceptions\MismatchProtocolBehaviorException;
use ArrayAccess\RdapClient\Interfaces\RdapClientInterface;
use ArrayAccess\RdapClient\Interfaces\RdapRequestInterface;
use ArrayAccess\RdapClient\Interfaces\RdapResponseInterface;
use ArrayAccess\RdapClient\Response\Ipv4Response;
use ArrayAccess\RdapClient\Services\Ipv4Service;
use function get_class;
use function sprintf;

class IPv4Protocol extends AbstractIPProtocol
{
    /**
     * Constructor
     *
     * @param RdapClientInterface $client
     * @param Ipv4Service|null $service
     */
    public function __construct(RdapClientInterface $client, ?Ipv4Service $service = null)
    {
        parent::__construct($client);
        if ($service) {
            $this->services = $service;
        }
    }

    /**
     * Set service
     *
     * @param Ipv4Service $service
     * @return void
     */
    public function setService(Ipv4Service $service): void
    {
        $this->services = $service;
    }

    /**
     * @return Ipv4Service
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @throws \Exception
     */
    public function getService(): Ipv4Service
    {
        if (!isset($this->services) || !($this->services instanceof Ipv4Service)) {
            $this->services = Ipv4Service::fromURL(self::IPV4_URI);
        }
        return $this->services;
    }

    /**
     * Create response object
     *
     * @param string $response
     * @param RdapRequestInterface $request
     * @return RdapResponseInterface
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

        return new Ipv4Response($response, $request, $this);
    }
}
