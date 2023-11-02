<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataString;
use Stringable;

class EventActor extends AbstractRdapResponseDataString
{
    protected string $name = 'eventActor';

    public function __construct(string|Stringable $data)
    {
        $this->values = (string) $data;
    }
}
