<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces;

use JsonSerializable;

interface RdapResponseInterface extends JsonSerializable
{
    public const CONTENT_TYPE = 'application/rdap+json';

    /**
     * @param string $response
     * @param RdapRequestInterface $request
     * @param RdapProtocolInterface $protocol
     */
    public function __construct(
        string $response,
        RdapRequestInterface $request,
        RdapProtocolInterface $protocol
    );

    /**
     * Get the allowed keys
     * @return ?array<array-key, string>
     */
    public function getAllowedKeys() : ?array;

    /**
     * Get the response JSON
     * @return string The response JSON representation
     */
    public function getResponseJson(): string;

    /**
     * Get the response array
     * @return array<array-key, mixed> The response array representation
     */
    public function getResponseArray() : array;

    /**
     * Get the request
     * @return RdapRequestInterface
     */
    public function getRequest(): RdapRequestInterface;

    /**
     * Get the protocol
     *
     * @return RdapProtocolInterface
     */
    public function getProtocol(): RdapProtocolInterface;

    /**
     * Get the definition
     * @return RdapResponseDefinitionInterface
     */
    public function getDefinition() : RdapResponseDefinitionInterface;
}
