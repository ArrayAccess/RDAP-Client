<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Abstracts;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataRecursiveEmptyNameInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

abstract class AbstractRdapResponseDataRecursiveArrayEmptyName implements
    RdapResponseDataRecursiveEmptyNameInterface,
    IteratorAggregate
{
    use AllowedKeyDataTraits;

    /**
     * @var array<mixed|RdapResponseDataInterface>
     */
    protected array $values;

    final public function getName(): void
    {
    }

    public function rootOnly() : bool
    {
        return false;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function jsonSerialize(): array
    {
        return $this->getValues();
    }

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

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->getValues());
    }
}
