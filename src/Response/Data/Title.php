<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataString;
use Stringable;

class Title extends AbstractRdapResponseDataString
{
    protected string $name = 'title';

    public function __construct(string|Stringable $data)
    {
        $this->values = (string) $data;
    }
}
