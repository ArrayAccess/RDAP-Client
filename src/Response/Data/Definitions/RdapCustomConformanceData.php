<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Definitions;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataNamedInterface;
use ArrayAccess\RdapClient\Response\Data\NonStandards\CustomNamedData;
use ArrayAccess\RdapClient\Response\Data\NonStandards\CustomUnnamedArrayData;
use ArrayAccess\RdapClient\Response\Data\NonStandards\CustomUnNamedData;
use function is_array;
use function is_iterable;

class RdapCustomConformanceData implements RdapResponseDataNamedInterface
{

    protected ?RdapResponseDataInterface $values = null;

    /**
     * @param string $name
     * @param mixed $data
     */
    public function __construct(
        protected string $name,
        mixed $data
    ) {
        if (is_array($data) || is_iterable($data)) {
            $values = [];
            foreach ($data as $key => $datum) {
                $values[] = new CustomNamedData($key, $datum);
            }
            $this->values = new CustomUnnamedArrayData(...$values);
            return;
        }
        $this->values = new CustomUnNamedData($data);
    }

    /**
     * @inheritDoc
     */
    public function rootOnly(): bool
    {
        return false;
    }


    /**
     * @inheritDoc
     * @return ?array<array-key, mixed>
     */
    public function getAllowedKeys(): ?array
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getValues(): ?RdapResponseDataInterface
    {
        return $this->values;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): ?RdapResponseDataInterface
    {
        return $this->getValues();
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getPlainData() : mixed
    {
        return $this->getValues()?->getPlainData();
    }
}
