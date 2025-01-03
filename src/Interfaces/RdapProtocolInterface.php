<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces;

interface RdapProtocolInterface
{
    public const IPV4_URI = 'https://data.iana.org/rdap/ipv4.json';
    public const IPV6_URI = 'https://data.iana.org/rdap/ipv6.json';
    public const ASN_URI = 'https://data.iana.org/rdap/asn.json';
    public const DOMAIN_URI = 'https://data.iana.org/rdap/dns.json';
    public const NS_URI = self::DOMAIN_URI;

    /**
     * Get the client
     *
     * @param RdapClientInterface $client
     */
    public function __construct(RdapClientInterface $client);

    /**
     * Get client
     *
     * @return RdapClientInterface
     */
    public function getClient(): RdapClientInterface;

    /**
     * Get the name
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Get the service
     * @return RdapServiceInterface
     */
    public function getService() : RdapServiceInterface;

    /**
     * Find the URL for the given target
     *
     * @param string $target
     * @return ?string
     */
    public function getFindURL(string $target) : ?string;

    /**
     * Find the RDAP request for the given target
     *
     * @param string $target
     * @return ?RdapRequestInterface
     */
    public function find(string $target) : ?RdapRequestInterface;

    /**
     * Get the search path
     *
     * @return string
     */
    public function getSearchPath() : string;

    /**
     * Create the RDAP response
     *
     * @param string $response
     * @param RdapRequestInterface $request
     * @return RdapResponseInterface
     */
    public function createResponse(
        string $response,
        RdapRequestInterface $request
    ) : RdapResponseInterface;
}
