<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Definitions;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataConformanceDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayIterator;
use Traversable;

/**
 * @template-implements RdapResponseDataConformanceDataInterface<string, RdapCustomConformanceData>
 */
class RdapCustomConformanceDataCollection implements RdapResponseDataConformanceDataInterface
{

    /**
     * @var array<string, RdapCustomConformanceData> $values The values of the data
     */
    protected array $values = [];

    /**
     * @param RdapCustomConformanceData ...$data
     */
    public function __construct(
        RdapCustomConformanceData ...$data
    ) {
        $this->values = [];
        foreach ($data as $item) {
            $this->values[$item->getName()] = $item;
        }
    }

    /**
     * Create a new instance of the class
     *
     * @param string $name
     * @param mixed $data
     * @return RdapCustomConformanceData
     */
    public static function createCustomConformanceData(
        string $name,
        mixed $data
    ) : RdapCustomConformanceData {
        return new RdapCustomConformanceData($name, $data);
    }

    /**
     * Add a new value to the collection
     *
     * @param RdapCustomConformanceData $data
     * @return void
     */
    public function add(RdapCustomConformanceData $data): void
    {
        $this->values[$data->getName()] = $data;
    }

    /**
     * Add a new value to the collection
     *
     * @param string $name
     * @param mixed $data
     * @return RdapCustomConformanceData
     */
    public function addFromData(string $name, mixed $data): RdapCustomConformanceData
    {
        $data = self::createCustomConformanceData($name, $data);
        $this->add($data);
        return $data;
    }

    /**
     * @return true
     */
    public function rootOnly(): bool
    {
        return true;
    }

    /**
     * @return ?array<array-key, mixed>
     */
    public function getAllowedKeys(): ?array
    {
        return null;
    }

    /**
     * @return array<string, RdapCustomConformanceData>
     */
    public function getValues() : array
    {
        return $this->values;
    }

    /**
     * @return array<string, RdapCustomConformanceData>
     */
    public function jsonSerialize(): array
    {
        return $this->getValues();
    }

    /**
     * @return void
     */
    public function getName(): void
    {
    }

    /**
     * @return array<string, mixed>
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
        /**
         * @var array<string, mixed>
         */
        return $values;
    }

    /**
     * @return Traversable<string, RdapCustomConformanceData>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->getValues());
    }
}
