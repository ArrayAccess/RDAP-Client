<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataString;
use Stringable;

class EventAction extends AbstractRdapResponseDataString
{
    /**
     * @var string $name
     */
    protected string $name = 'eventAction';

    /**
     * @param string|Stringable $data
     */
    public function __construct(string|Stringable $data)
    {
        $this->values = (string) $data;
    }
}
