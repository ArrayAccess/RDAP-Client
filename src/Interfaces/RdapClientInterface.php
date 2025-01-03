<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces;

interface RdapClientInterface
{
    public const IPV4 = 'ipv4';
    public const IPV6 = 'ipv46';
    public const DOMAIN = 'domain';
    public const NS = 'ns';
    public const ASN = 'asn';
}
