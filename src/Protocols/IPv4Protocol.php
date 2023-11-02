<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Protocols;

use ArrayAccess\RdapClient\Exceptions\MismatchProtocolBehaviorException;
use ArrayAccess\RdapClient\Interfaces\RdapClientInterface;
use ArrayAccess\RdapClient\Interfaces\RdapRequestInterface;
use ArrayAccess\RdapClient\Interfaces\RdapResponseInterface;
use ArrayAccess\RdapClient\Response\Ipv4Response;
use ArrayAccess\RdapClient\Services\Ipv4Service;
use Exception;
use function get_class;
use function sprintf;

class IPv4Protocol extends AbstractIPProtocol
{
    public function __construct(RdapClientInterface $client, ?Ipv4Service $service = null)
    {
        parent::__construct($client);
        if ($service) {
            $this->services = $service;
        }
    }

    public function setService(Ipv4Service $service): void
    {
        $this->services = $service;
    }

    /**
     * @throws Exception
     */
    public function getService(): Ipv4Service
    {
        return $this->services ??= Ipv4Service::fromURL(self::IPV4_URI);
    }

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
