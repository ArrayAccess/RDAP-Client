<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

interface RdapResponseDataNamedInterface extends RdapResponseDataInterface
{
    /**
     * @inheritDoc
     * @return string
     */
    public function getName() : string;
}
