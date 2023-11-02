<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArray;
use ArrayAccess\RdapClient\Response\Data\Definitions\EntityDefinitionObjectClassName;
use function array_values;

class Entities extends AbstractRdapResponseDataRecursiveArray
{
    protected string $name = 'entities';

    public function __construct(EntityDefinitionObjectClassName ...$data)
    {
        $this->values = array_values($data);
    }
}
