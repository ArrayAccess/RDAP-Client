<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

interface RdapResponseDataRecursiveEmptyNameInterface extends RdapResponseDataRecursiveInterface
{
    /**
     * @inheritDoc
     * @return void
     */
    public function getName() : void;

    /**
     * @return RdapResponseDataInterface|array<array-key, mixed>
     */
    public function getValues() : RdapResponseDataInterface|array;
}
