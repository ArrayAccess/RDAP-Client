<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

use IteratorAggregate;

/**
 * @template TKey
 * @template TValue
 * @template-extends IteratorAggregate<TKey, TValue>
 */
interface RdapResponseDataConformanceDataInterface extends
    RdapResponseDataInterface,
    IteratorAggregate
{
    /**
     * @return array<array-key, mixed>
     */
    public function getValues() : array;

    /**
     * Add a new value to the collection
     *
     * @param string $name
     * @param mixed $data
     * @return RdapResponseDataInterface
     */
    public function addFromData(string $name, mixed $data): RdapResponseDataInterface;
}
