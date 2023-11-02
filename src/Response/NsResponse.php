<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response;

use ArrayAccess\RdapClient\Response\Abstracts\AbstractResponse;
use ArrayAccess\RdapClient\Response\Definitions\NsDefinition;

class NsResponse extends AbstractResponse
{
    protected ?NsDefinition $definition;

    public function getDefinition() : NsDefinition
    {
        return $this->definition ??= new NsDefinition($this);
    }
}
