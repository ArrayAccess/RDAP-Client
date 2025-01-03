<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

use JsonSerializable;

interface RdapResponseDataInterface extends JsonSerializable
{
    /**
     * Determine if the data is root only
     * @return bool
     */
    public function rootOnly() : bool;

    /**
     * @return array<array-key, string>|null
     */
    public function getAllowedKeys() : ?array;

    /**
     * Get name
     */
    public function getName(); // @phpstan-ignore-line

    /**
     * Get values of the data
     */
    public function getValues(); // @phpstan-ignore-line

    /**
     * Get plain data
     */
    public function getPlainData(); // @phpstan-ignore-line
}
