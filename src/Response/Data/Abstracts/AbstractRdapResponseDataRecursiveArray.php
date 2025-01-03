<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Abstracts;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataRecursiveNamedInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @template-implements IteratorAggregate<array-key, mixed>
 */
abstract class AbstractRdapResponseDataRecursiveArray implements
    RdapResponseDataRecursiveNamedInterface,
    IteratorAggregate
{
    use AllowedKeyDataTraits;

    /**
     * @var string $name The name of the data
     */
    protected string $name;

    /**
     * @var array<array-key, mixed> $values The values of the data
     */
    protected array $values;

    /**
     * @inheritDoc
     */
    public function rootOnly() : bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get values of the data
     * @return array<array-key, mixed>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->getValues();
    }

    /**
     * Get plain data
     * @return array<array-key, mixed>
     */
    public function getPlainData(): array
    {
        /** @noinspection DuplicatedCode */
        $values = [];
        foreach ($this->getValues() as $key => $value) {
            if ($value instanceof RdapResponseDataInterface) {
                $name = $value->getName();
                if ($name === null) {
                    $values[] = $value->getPlainData();
                    continue;
                }
                $values[$name] = $value->getPlainData();
                continue;
            }
            $values[$key] = $value;
        }
        return $values;
    }

    /**
     * @return Traversable<array-key, mixed>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->getValues());
    }
}
