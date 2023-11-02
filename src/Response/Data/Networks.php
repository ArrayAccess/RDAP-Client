<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArray;
use ArrayAccess\RdapClient\Response\Data\Definitions\RemarksDefinition;
use function array_values;

class Networks extends AbstractRdapResponseDataRecursiveArray
{
    protected string $name = 'networks';

    public function __construct(RemarksDefinition ...$data)
    {
        $this->values = array_values($data);
    }
}
