<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Definitions;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataNamedInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataStringableInterface;
use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveNamed;
use ArrayAccess\RdapClient\Response\Data\NonStandards\ObjectData;

class NamedRecursiveObjectData extends AbstractRdapResponseDataRecursiveNamed
{
    public function __construct(
        RdapResponseDataNamedInterface|RdapResponseDataStringableInterface ...$data
    ) {
        $this->values = new ObjectData(...$data);
    }
}
