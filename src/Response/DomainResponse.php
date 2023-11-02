<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response;

use ArrayAccess\RdapClient\Response\Abstracts\AbstractResponse;
use ArrayAccess\RdapClient\Response\Definitions\DomainDefinition;

class DomainResponse extends AbstractResponse
{
    protected ?DomainDefinition $definition;

    public function getDefinition(): DomainDefinition
    {
        return $this->definition ??= new DomainDefinition($this);
    }
}
