<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

interface RdapResponseDataRecursiveInterface extends RdapResponseDataInterface
{
    public function getValues() : RdapResponseDataInterface|array;
}
