<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

interface RdapResponseDataNamedInterface extends RdapResponseDataInterface
{
    public function getName() : string;
}
