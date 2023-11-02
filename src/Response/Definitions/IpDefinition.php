<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Definitions;

use ArrayAccess\RdapClient\Response\Data\EndAddress;
use ArrayAccess\RdapClient\Response\Data\IpVersion;
use ArrayAccess\RdapClient\Response\Data\Port43;
use ArrayAccess\RdapClient\Response\Data\StartAddress;

class IpDefinition extends AbstractResponseDefinition
{
    protected ?IpVersion $ipVersion = null;

    protected ?Port43 $port43 = null;

    protected ?StartAddress $startAddress = null;

    protected ?EndAddress $endAddress = null;

    public function getIpVersion(): ?IpVersion
    {
        return $this->ipVersion;
    }

    public function getPort43(): ?Port43
    {
        return $this->port43;
    }

    public function getStartAddress(): ?StartAddress
    {
        return $this->startAddress;
    }

    public function getEndAddress(): ?EndAddress
    {
        return $this->endAddress;
    }
}
