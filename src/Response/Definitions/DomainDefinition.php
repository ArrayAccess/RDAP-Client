<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Definitions;

use ArrayAccess\RdapClient\Response\Data\LdhName;
use ArrayAccess\RdapClient\Response\Data\NameServers;
use ArrayAccess\RdapClient\Response\Data\SecureDNS;

class DomainDefinition extends AbstractResponseDefinition
{
    protected ?LdhName $ldhName = null;

    protected ?SecureDNS $secureDNS = null;

    protected ?NameServers $nameservers = null;

    public function getLdhName(): ?LdhName
    {
        return $this->ldhName;
    }

    public function getSecureDNS(): ?SecureDNS
    {
        return $this->secureDNS;
    }

    public function getNameservers(): ?NameServers
    {
        return $this->nameservers;
    }
}
