<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

use Stringable;

interface RdapResponseDataStringableInterface extends RdapResponseDataInterface, Stringable
{
    public function getValues() : string|Stringable|null;

    public function getStringData() : ?string;
}
