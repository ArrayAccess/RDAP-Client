<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Definitions;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataNamedInterface;
use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArrayEmptyName;

class DsDataDefinition extends AbstractRdapResponseDataRecursiveArrayEmptyName
{
    public function __construct(
        RdapResponseDataNamedInterface ...$values
    ) {
        foreach ($values as $value) {
            $this->values[$value->getName()] = $value;
        }
    }
}
