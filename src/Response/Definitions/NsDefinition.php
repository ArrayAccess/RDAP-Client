<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Definitions;

use ArrayAccess\RdapClient\Response\Data\IpAddresses;
use ArrayAccess\RdapClient\Response\Data\LdhName;

class NsDefinition extends AbstractResponseDefinition
{
    protected ?LdhName $ldhName = null;

    protected ?IpAddresses $ipAddresses = null;

    public function getLdhName(): ?LdhName
    {
        return $this->ldhName;
    }

    public function getIpAddresses(): ?IpAddresses
    {
        return $this->ipAddresses;
    }
}
