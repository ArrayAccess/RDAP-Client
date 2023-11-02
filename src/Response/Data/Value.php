<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractArrayOrStringData;

class Value extends AbstractArrayOrStringData
{
    protected string $name = 'value';
}
