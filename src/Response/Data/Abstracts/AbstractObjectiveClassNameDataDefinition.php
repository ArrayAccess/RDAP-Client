<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Abstracts;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataNamedInterface;
use ArrayAccess\RdapClient\Response\Data\ObjectClassName;

/**
 * @template T of string
 */
abstract class AbstractObjectiveClassNameDataDefinition extends AbstractRdapResponseDataRecursiveArrayEmptyName
{

    /**
     * @param RdapResponseDataNamedInterface ...$values
     */
    public function __construct(
        RdapResponseDataNamedInterface ...$values
    ) {
        $object = $this->getObjectClassName();
        foreach ($values as $val) {
            $this->values[$val->getName()] = $val;
        }
        if (!isset($this->values[$object->getName()])
            || $object->getName() !== $this->values['objectClassName']->getName()
        ) {
            $this->values[$object->getName()] = $object;
        }
    }

    /**
     * @return ObjectClassName<T>
     */
    abstract public function getObjectClassName(): ObjectClassName;

    /**
     * @return array<array-key, mixed>
     */
    public function getPlainData(): array
    {
        $values = [
            $this->getObjectClassName()->getName() => $this->getObjectClassName()->getPlainData()
        ];
        foreach ($this->getValues() as $value) {
            $name = $value->getName();
            if ($name === null) {
                $values[] = $value->getPlainData();
            }
            $values[$name] = $value->getPlainData();
        }
        return $values;
    }
}
