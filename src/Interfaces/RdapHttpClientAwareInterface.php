<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces;

use Psr\Http\Client\ClientInterface;
use Psr\SimpleCache\CacheInterface;

interface RdapHttpClientAwareInterface
{
    /**
     * Get the PSR-18 HTTP client
     *
     * @return ClientInterface|null null when the built-in stream wrapper is used
     */
    public function getHttpClient() : ?ClientInterface;

    /**
     * Get the PSR-16 cache used for the IANA bootstrap files
     *
     * @return CacheInterface|null null when the built-in temporary file cache is used
     */
    public function getCache() : ?CacheInterface;

    /**
     * Fetch the body of the given URL
     *
     * The body is returned regardless of the HTTP status code,
     * so RDAP error responses (RFC 9083 section 6) can be parsed.
     *
     * @param string $url
     * @return string
     * @throws \ArrayAccess\RdapClient\Exceptions\RdapRemoteRequestException
     */
    public function fetch(string $url) : string;
}
