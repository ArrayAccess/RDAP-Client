<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

interface RdapResponseDataObjectDataClassNameInterface extends RdapResponseDataStringableInterface
{
    public function getName(): string;
}
