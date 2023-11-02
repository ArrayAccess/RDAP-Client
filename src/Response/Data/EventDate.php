<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractDateData;

class EventDate extends AbstractDateData
{
    protected string $name = 'eventDate';
}
