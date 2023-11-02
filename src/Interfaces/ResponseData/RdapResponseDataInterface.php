<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

use JsonSerializable;

interface RdapResponseDataInterface extends JsonSerializable
{
    public function rootOnly() : bool;

    public function getAllowedKeys() : ?array;

    public function getName();

    public function getValues();

    public function jsonSerialize(): mixed;

    public function getPlainData();
}
