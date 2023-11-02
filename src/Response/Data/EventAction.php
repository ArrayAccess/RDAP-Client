<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataString;
use Stringable;

class EventAction extends AbstractRdapResponseDataString
{
    protected string $name = 'eventAction';

    public function __construct(string|Stringable $data)
    {
        $this->values = (string) $data;
    }
}
