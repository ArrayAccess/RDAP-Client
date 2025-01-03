<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Definitions;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArrayEmptyName;
use ArrayAccess\RdapClient\Response\Data\EventAction;
use ArrayAccess\RdapClient\Response\Data\EventActor;
use ArrayAccess\RdapClient\Response\Data\EventDate;
use ArrayAccess\RdapClient\Response\Data\Links;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @template-implements IteratorAggregate<array{
 *     eventAction?: EventAction,
 *     eventActor?: EventActor,
 *     eventDate?: EventDate,
 *     links?: Links,
 * }>
 * @template-implements IteratorAggregate<string, EventAction|EventActor|EventDate|Links>
 */
class EventsCollection extends AbstractRdapResponseDataRecursiveArrayEmptyName implements IteratorAggregate
{
    /**
     * @var array<array-key, string> $allowedKeys
     */
    protected array $allowedKeys = [
        'eventAction', // required
        'eventActor',
        'eventDate', // required
        'links',
    ];

    /**
     * @var array{
     *     eventAction?: EventAction,
     *     eventActor?: EventActor,
     *     eventDate?: EventDate,
     *     links?: Links,
     * } $values
     */
    protected array $values = [];

    public function __construct(EventActor|EventAction|EventDate|Links ...$data)
    {
        foreach ($data as $action) {
            if ($action instanceof EventActor) {
                $this->values['eventActor'] = $action;
                continue;
            }
            if ($action instanceof EventDate) {
                $this->values['eventDate'] = $action;
                continue;
            }
            if ($action instanceof Links) {
                $this->values['links'] = $action;
                continue;
            }
            $this->values['eventAction'] = $action;
        }
        $this->values = array_filter($this->values);
    }

    /**
     * @return EventAction|null
     */
    public function getAction() : ?EventAction
    {
        return $this->values['eventAction']??null;
    }

    /**
     * @return EventActor|null
     */
    public function getActor() : ?EventActor
    {
        return $this->values['eventActor']??null;
    }

    /**
     * @return EventDate|null
     */
    public function getDate() : ?EventDate
    {
        return $this->values['eventDate']??null;
    }

    /**
     * @return Links|null
     */
    public function getLinks() : ?Links
    {
        return $this->values['links']??null;
    }

    /**
     * @return array{
     *      eventAction?: EventAction,
     *      eventActor?: EventActor,
     *      eventDate?: EventDate,
     *      links?: Links,
     *  }
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return Traversable<"eventAction"|"eventActor"|"eventDate"|"links", EventAction|EventActor|EventDate|Links>
     * @return Traversable<string, EventAction|EventActor|EventDate|Links>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->getValues());
    }
}
