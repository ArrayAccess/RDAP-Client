<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Definitions;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractObjectiveClassNameDataDefinition;
use ArrayAccess\RdapClient\Response\Data\ObjectClassName;

/**
 * @template-extends AbstractObjectiveClassNameDataDefinition<"entity">
 */
class EntityDefinitionObjectClassName extends AbstractObjectiveClassNameDataDefinition
{
    /**
     * @return ObjectClassName<"entity"> the object class name
     */
    public function getObjectClassName(): ObjectClassName
    {
        return $this->values['entity'] ??= new ObjectClassName('entity');
    }
}
