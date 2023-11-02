<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response;

use ArrayAccess\RdapClient\Response\Abstracts\AbstractResponse;
use ArrayAccess\RdapClient\Response\Definitions\AsnDefinition;

class AsnResponse extends AbstractResponse
{
    protected ?AsnDefinition $definition;

    public function getDefinition(): AsnDefinition
    {
        return $this->definition ??= new AsnDefinition($this);
    }
}
