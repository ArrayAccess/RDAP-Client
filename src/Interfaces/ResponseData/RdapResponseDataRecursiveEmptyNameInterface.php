<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

interface RdapResponseDataRecursiveEmptyNameInterface extends RdapResponseDataRecursiveInterface
{
    public function getName() : void;

    public function getValues() : RdapResponseDataInterface|array;
}
