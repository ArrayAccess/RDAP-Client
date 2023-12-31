<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces;

interface RdapResponseInterface
{
    const CONTENT_TYPE = 'application/rdap+json';

    public function __construct(
        string $response,
        RdapRequestInterface $request,
        RdapProtocolInterface $protocol
    );

    public function getAllowedKeys() : ?array;

    public function getResponseJson(): string;

    public function getRequest(): RdapRequestInterface;

    public function getProtocol(): RdapProtocolInterface;

    public function getDefinition() : RdapResponseDefinitionInterface;
}
