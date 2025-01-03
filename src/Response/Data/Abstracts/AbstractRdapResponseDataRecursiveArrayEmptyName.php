<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Abstracts;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataRecursiveEmptyNameInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @template-implements IteratorAggregate<array-key, mixed>
 */
abstract class AbstractRdapResponseDataRecursiveArrayEmptyName implements
    RdapResponseDataRecursiveEmptyNameInterface,
    IteratorAggregate
{
    use AllowedKeyDataTraits;

    /**
     * @var array<array-key, mixed|RdapResponseDataInterface>
     */
    protected array $values;

    /**
     * @inheritDoc
     */
    final public function getName(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function rootOnly() : bool
    {
        return false;
    }

    /**
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
