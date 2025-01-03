<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArray;
use ArrayAccess\RdapClient\Response\Data\Definitions\EventsCollection;
use IteratorAggregate;
use function array_values;

/**
 * @template-implements IteratorAggregate<array-key, EventsCollection>
 */
class Events extends AbstractRdapResponseDataRecursiveArray implements IteratorAggregate
{
    /**
     * @var string $name
     */
    protected string $name = 'events';

    /**
     * @var ?array<string>
     */
    protected ?array $allowedKeys = null;

    /**
     * @var EventsCollection[]
     */
    protected array $values = [];

    /**
     * @param EventsCollection ...$data
     */
    public function __construct(EventsCollection ...$data)
    {
        $this->values = array_values($data);
    }
}
