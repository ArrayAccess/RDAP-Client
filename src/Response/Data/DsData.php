<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArray;
use ArrayAccess\RdapClient\Response\Data\Definitions\DsDataDefinition;
use function array_values;

class DsData extends AbstractRdapResponseDataRecursiveArray
{
    protected string $name = 'dsData';

    public function __construct(DsDataDefinition ...$data)
    {
        $this->values = array_values($data);
    }
}
