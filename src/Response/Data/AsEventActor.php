<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArray;
use ArrayAccess\RdapClient\Response\Data\Definitions\EventsCollection;
use function array_values;

class AsEventActor extends AbstractRdapResponseDataRecursiveArray
{
    /**
     * @var string $name
     */
    protected string $name = 'asEventActor';

    /**
     * @var array<string>|null
     */
    protected ?array $allowedKeys = null;

    /**
     * @param EventsCollection ...$data
     */
    public function __construct(EventsCollection ...$data)
    {
        $this->values = array_values($data);
    }
}
