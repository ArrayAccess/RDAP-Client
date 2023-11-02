<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Definitions;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataConformanceDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayIterator;
use Traversable;

class RdapCustomConformanceDataCollection implements RdapResponseDataConformanceDataInterface
{

    protected array $values = [];

    public function __construct(
        RdapCustomConformanceData ...$data
    ) {
        $this->values = [];
        foreach ($data as $item) {
            $this->values[$item->getName()] = $item;
        }
    }

    public static function createCustomConformanceData(
        string $name,
        $data
    ) : RdapCustomConformanceData {
        return new RdapCustomConformanceData($name, $data);
    }

    public function add(RdapCustomConformanceData $data): void
    {
        $this->values[$data->getName()] = $data;
    }

    public function addFromData(string $name, $data): RdapCustomConformanceData
    {
        $data = self::createCustomConformanceData($name, $data);
        $this->add($data);
        return $data;
    }

    public function rootOnly(): bool
    {
        return true;
    }

    public function getAllowedKeys(): ?array
    {
        return null;
    }

    public function getValues() : array
    {
        return $this->values;
    }

    public function jsonSerialize(): array
    {
        return $this->getValues();
    }

    public function getName(): void
    {
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
