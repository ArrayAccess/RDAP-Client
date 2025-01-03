<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

use Stringable;

interface RdapResponseDataStringableInterface extends RdapResponseDataInterface, Stringable
{
    /**
     * Get values
     * @return string|Stringable|null
     */
    public function getValues() : string|Stringable|null;

    /**
     * Get string data
     *
     * @return string|null
     */
    public function getStringData() : ?string;
}
