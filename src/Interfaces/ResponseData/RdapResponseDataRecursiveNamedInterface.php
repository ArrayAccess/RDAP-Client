<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

interface RdapResponseDataRecursiveNamedInterface extends
    RdapResponseDataRecursiveInterface,
    RdapResponseDataNamedInterface
{
    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return RdapResponseDataInterface|array|RdapResponseDataInterface[]
     */
    public function getValues() : RdapResponseDataInterface|array;
}
