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

    protected string $name;

    protected array|string|Stringable $values;

    public function __construct($data)
    {
        $this->values = $data;
    }

    public function rootOnly() : bool
    {
        return false;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValues(): array|string|Stringable
    {
        return $this->values;
    }

    public function jsonSerialize() : array|string|Stringable
    {
        return $this->getValues();
    }


    public function getPlainData()
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
