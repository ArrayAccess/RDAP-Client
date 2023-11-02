<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces;

interface RdapProtocolInterface
{
    const IPV4_URI = 'https://data.iana.org/rdap/ipv4.json';
    const IPV6_URI = 'https://data.iana.org/rdap/ipv6.json';
    const ASN_URI = 'https://data.iana.org/rdap/asn.json';
    const DOMAIN_URI = 'https://data.iana.org/rdap/dns.json';

    const NS_URI = self::DOMAIN_URI;

    public function __construct(RdapClientInterface $client);

    public function getName() : string;

    public function getService() : RdapServiceInterface;

    public function getFindURL(string $target) : ?string;

    public function find(string $target) : ?RdapRequestInterface;

    public function getSearchPath() : string;

    public function createResponse(
        string $response,
        RdapRequestInterface $request
    ) : RdapResponseInterface;
}
