<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataString;
use Stringable;

class Country extends AbstractRdapResponseDataString
{
    protected string $name = 'country';

    public function __construct(string|Stringable $data)
    {
        $this->values = (string) $data;
    }
}
