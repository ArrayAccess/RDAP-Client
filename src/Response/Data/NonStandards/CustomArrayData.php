<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\NonStandards;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;

class CustomArrayData implements RdapResponseDataInterface
{
    use AllowedKeyDataTraits;

    /**
     * @var CustomNamedData[]|CustomUnNamedData[]
     */
    protected array $data;

    public function rootOnly() : bool
    {
        return false;
    }

    public function __construct(
        protected string $name,
        CustomNamedData|CustomUnNamedData...$data
    ) {
        $this->data = [];
        foreach ($data as $item) {
            $name = $item->getName();
            if ($name === null) {
                $this->data[] = $item;
                continue;
            }
            $this->data[$name] = $item;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValues(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): array
    {
        return $this->data;
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
}
