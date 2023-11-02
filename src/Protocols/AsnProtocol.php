<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Protocols;

use ArrayAccess\RdapClient\Exceptions\MismatchProtocolBehaviorException;
use ArrayAccess\RdapClient\Interfaces\RdapClientInterface;
use ArrayAccess\RdapClient\Interfaces\RdapRequestInterface;
use ArrayAccess\RdapClient\Interfaces\RdapResponseInterface;
use ArrayAccess\RdapClient\Response\AsnResponse;
use ArrayAccess\RdapClient\Services\AsnService;
use Exception;
use function get_class;
use function sprintf;

class AsnProtocol extends AbstractRdapProtocol
{
    public function __construct(RdapClientInterface $client, ?AsnService $service = null)
    {
        parent::__construct($client);
        if ($service) {
            $this->services = $service;
        }
    }

    /**
     * @throws Exception
     */
    public function getService(): AsnService
    {
        return $this->services ??= AsnService::fromURL(self::ASN_URI);
    }

    /**
     * @return string
     * @link https://datatracker.ietf.org/doc/html/rfc7482#section-3.1.2
     */
    public function getSearchPath(): string
    {
        return '/autnum';
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

        return new AsnResponse($response, $request, $this);
    }
}
