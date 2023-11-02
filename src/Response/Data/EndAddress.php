<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataString;
use Stringable;

class EndAddress extends AbstractRdapResponseDataString
{
    protected string $name = 'endAddress';

    public function __construct(string|Stringable $data)
    {
        $this->values = (string) $data;
    }
}
