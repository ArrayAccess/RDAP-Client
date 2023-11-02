<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

interface RdapResponseDataIntegerInterface extends RdapResponseDataInterface
{
    public function getValues() : int;
}
