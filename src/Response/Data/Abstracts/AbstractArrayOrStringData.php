<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Abstracts;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataNamedInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;
use Stringable;
use function is_array;

abstract class AbstractArrayOrStringData implements RdapResponseDataNamedInterface
{
    use AllowedKeyDataTraits;

    /**
     * @var string $name name of the data
     */
    protected string $name;

    /**
     * @var array<array-key, mixed>|string|Stringable $values
     */
    protected array|string|Stringable $values;

    /**
     * @param array<array-key, mixed>|string|Stringable $data
     */
    public function __construct(array|string|Stringable $data)
    {
        $this->values = $data;
    }

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
     * @return array<array-key, mixed>|string|Stringable
     */
    public function getValues(): array|string|Stringable
    {
        return $this->values;
    }

    /**
     * @return array<array-key, mixed>|string|Stringable
     */
    public function jsonSerialize() : array|string|Stringable
    {
        return $this->getValues();
    }

    /**
     * @return array<array-key, mixed>|string|Stringable
     */
    public function getPlainData() : array|string|Stringable
    {
        /** @noinspection DuplicatedCode */
        if (is_array($this->values)) {
            $values = [];
            foreach ($this->values as $key => $value) {
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
        return $this->values instanceof RdapResponseDataInterface
            ? $this->values->getPlainData()
            : $this->values;
    }
}
