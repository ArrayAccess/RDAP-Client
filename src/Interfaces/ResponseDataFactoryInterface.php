<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;

interface ResponseDataFactoryInterface
{
    public function create(string $key, $arg, ...$data) : RdapResponseDataInterface;
}
