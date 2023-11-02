<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Abstracts;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataNamedInterface;
use ArrayAccess\RdapClient\Response\Data\ObjectClassName;

abstract class AbstractObjectiveClassNameDataDefinition extends AbstractRdapResponseDataRecursiveArrayEmptyName
{
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

    abstract public function getObjectClassName(): ObjectClassName;

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
