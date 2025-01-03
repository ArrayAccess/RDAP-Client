<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\NonStandards;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;

class CustomUnnamedArrayData implements RdapResponseDataInterface
{
    use AllowedKeyDataTraits;

    /**
     * @var array<array-key, CustomNamedData|CustomUnNamedData>
     */
    protected array $data;

    /**
     * @inheritDoc
     * @return false
     */
    public function rootOnly() : bool
    {
        return false;
    }

    /**
     * @param CustomNamedData|CustomUnNamedData ...$data
     */
    public function __construct(
        CustomNamedData|CustomUnNamedData...$data
    ) {
        $this->data = [];
        foreach ($data as $item) {
            $name = $item->getName();
            if (!$name) {
                $this->data[] = $item;
                continue;
            }
            $this->data[$name] = $item;
        }
    }

    /**
     * @inheritDoc
     * @return void
     */
    public function getName(): void
    {
    }

    /**
     * @return array<array-key, CustomNamedData|CustomUnNamedData>
     */
    public function getValues(): array
    {
        return $this->data;
    }

    /**
     * @return array<array-key, CustomNamedData|CustomUnNamedData>
     */
    public function jsonSerialize(): array
    {
        return $this->data;
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
}
