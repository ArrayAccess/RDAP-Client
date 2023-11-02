<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractIntegerData;

class KeyTag extends AbstractIntegerData
{
    protected string $name = 'keyTag';
}
