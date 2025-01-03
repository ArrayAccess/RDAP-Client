<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Definitions;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractObjectiveClassNameDataDefinition;
use ArrayAccess\RdapClient\Response\Data\ObjectClassName;

/**
 * @template-extends AbstractObjectiveClassNameDataDefinition<"nameserver">
 */
class NameserverDefinitionObjectClassName extends AbstractObjectiveClassNameDataDefinition
{
    /**
     * @return ObjectClassName<"nameserver">
     */
    public function getObjectClassName(): ObjectClassName
    {
        return $this->values['nameserver'] ??= new ObjectClassName('nameserver');
    }
}
