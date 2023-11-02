<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Abstracts;

use ArrayAccess\RdapClient\Response\Definitions\IpDefinition;

abstract class AbstractIpResponse extends AbstractResponse
{
    abstract public function getVersion() : string;

    public function getDefinition(): IpDefinition
    {
        return $this->definition ??= new IpDefinition($this);
    }
}
