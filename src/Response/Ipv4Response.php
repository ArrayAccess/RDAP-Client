<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response;

use ArrayAccess\RdapClient\Response\Abstracts\AbstractIpResponse;

class Ipv4Response extends AbstractIpResponse
{
    /**
     * @return "ipv4" the IP version
     */
    public function getVersion() : string
    {
        return 'ipv4';
    }
}
