<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response;

use ArrayAccess\RdapClient\Response\Abstracts\AbstractIpResponse;

class Ipv6Response extends AbstractIpResponse
{
    /**
     * @return "ipv6" the IP version
     */
    public function getVersion() : string
    {
        return 'ipv6';
    }
}
