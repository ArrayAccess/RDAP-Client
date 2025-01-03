<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

interface RdapResponseDataRecursiveInterface extends RdapResponseDataInterface
{
    /**
     * @return RdapResponseDataInterface|array<array-key, RdapResponseDataInterface>
     */
    public function getValues() : RdapResponseDataInterface|array;
}
