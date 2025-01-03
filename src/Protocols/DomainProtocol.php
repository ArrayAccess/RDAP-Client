<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Protocols;

use ArrayAccess\RdapClient\Exceptions\MismatchProtocolBehaviorException;
use ArrayAccess\RdapClient\Interfaces\RdapClientInterface;
use ArrayAccess\RdapClient\Interfaces\RdapRequestInterface;
use ArrayAccess\RdapClient\Interfaces\RdapResponseInterface;
use ArrayAccess\RdapClient\Response\DomainResponse;
use ArrayAccess\RdapClient\Services\DomainService;
use function get_class;
use function sprintf;

class DomainProtocol extends AbstractRdapProtocol
{
    /**
     * @param RdapClientInterface $client
     * @param DomainService|null $service
     */
    public function __construct(RdapClientInterface $client, ?DomainService $service = null)
    {
        parent::__construct($client);
        if ($service) {
            $this->services = $service;
        }
    }

    /**
     * Set service
     *
     * @param DomainService $service
     * @return void
     */
    public function setService(DomainService $service): void
    {
        $this->services = $service;
    }

    /**
     * @return DomainService
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @throws \Exception
     */
    public function getService(): DomainService
    {
        if (!isset($this->services) || !($this->services instanceof DomainService)) {
            $this->services = DomainService::fromURL(self::DOMAIN_URI);
        }
        return $this->services;
    }

    /**
     * @return string
     * @link https://datatracker.ietf.org/doc/html/rfc7482#section-3.1.3
     */
    public function getSearchPath(): string
    {
        return '/domain';
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

        return new DomainResponse($response, $request, $this);
    }
}
