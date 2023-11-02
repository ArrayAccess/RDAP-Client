<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

use IteratorAggregate;

interface RdapResponseDataConformanceDataInterface extends
    RdapResponseDataInterface,
    IteratorAggregate
{
    public function getValues() : array;
}
