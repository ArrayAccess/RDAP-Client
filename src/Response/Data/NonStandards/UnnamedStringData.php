<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\NonStandards;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataStringEmptyName;
use Stringable;

class UnnamedStringData extends AbstractRdapResponseDataStringEmptyName
{
    public function __construct(string|Stringable $data)
    {
        $this->values = $data;
    }
}
