<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces;

use ArrayAccess\RdapClient\Client;

interface RdapRequestInterface
{
    const DEFAULT_STREAM_CONTEXT = [
        'http' => [
            'protocol_version' => '1.1',
            'method' => 'GET',
            'header' => [
                "Accept: application/json,text/html,application/xhtml+xml,application/xml,*/*;q=0.9",
                "Cache-Control: no-cache",
                "Connection: close",
                // use user agent
                "User-Agent: Rdap-Client/".Client::VERSION
            ],
            'timeout' => 15,
            'ignore_errors' => true
        ],
    ];

    public function __construct(string $target, RdapProtocolInterface $protocol);

    public function getProtocol() : RdapProtocolInterface;

    public function getTarget() : string;

    public function getResponse() : RdapResponseInterface;

    public function withRdapSearchURL(string $url) : static;
}
