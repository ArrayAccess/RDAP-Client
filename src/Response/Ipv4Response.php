<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response;

use ArrayAccess\RdapClient\Response\Abstracts\AbstractIpResponse;
use ArrayAccess\RdapClient\Response\Definitions\IpDefinition;

class Ipv4Response extends AbstractIpResponse
{
    protected ?IpDefinition $definition;

    public function getVersion() : string
    {
        return 'ipv4';
    }
}
