<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Abstracts;

use ArrayAccess\RdapClient\Response\Definitions\IpDefinition;

abstract class AbstractIpResponse extends AbstractResponse
{
    /**
     * @var IpDefinition|null $definition definition of IP
     */
    protected ?IpDefinition $definition;

    /**
     * Get IP version
     * @return "ipv4"|"ipv6"
     */
    abstract public function getVersion() : string;

    /**
     * @inheritDoc
     */
    public function getDefinition(): IpDefinition
    {
        return $this->definition ??= new IpDefinition($this);
    }
}
