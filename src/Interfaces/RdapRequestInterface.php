<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces;

use ArrayAccess\RdapClient\Client;

interface RdapRequestInterface
{
    public const DEFAULT_STREAM_CONTEXT = [
        'http' => [
            'protocol_version' => '1.1',
            'method' => 'GET',
            'header' => [
                "Accept: application/json,text/html,application/xhtml+xml,application/xml,*/*;q=0.9",
                "Cache-Control: no-cache",
                "Connection: close",
                // use user agent
                "User-Agent: Rdap-Client/" . Client::VERSION
            ],
            'timeout' => 15,
            'ignore_errors' => true
        ],
    ];

    /**
     * Construct the RDAP request
     * @param string $target
     * @param RdapProtocolInterface $protocol
     */
    public function __construct(string $target, RdapProtocolInterface $protocol);

    /**
     * Get the protocol
     *
     * @return RdapProtocolInterface
     */
    public function getProtocol() : RdapProtocolInterface;

    /**
     * Get the target
     *
     * @return string
     */
    public function getTarget() : string;

    /**
     * Get RDAP Response
     *
     * @return RdapResponseInterface
     */
    public function getResponse() : RdapResponseInterface;

    /**
     * Close the RDAP request
     *
     * @param string $url
     * @return $this
     */
    public function withRdapSearchURL(string $url) : static;

    /**
     * Get the error code
     * @return int 0 if no error
     */
    public function getErrorCode(): int;

    /**
     * Get the error message
     * @return string|null The error message
     */
    public function getErrorMessage(): ?string;

    /**
     * Get the RDAP search URL
     *
     * @return string|null The RDAP search URL
     */
    public function getRdapSearchURL(): ?string;
}
