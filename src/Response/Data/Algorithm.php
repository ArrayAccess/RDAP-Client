<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractIntegerData;

class Algorithm extends AbstractIntegerData
{
    protected string $name = 'algorithm';
}
