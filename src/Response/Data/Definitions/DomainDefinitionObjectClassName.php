<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Definitions;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractObjectiveClassNameDataDefinition;
use ArrayAccess\RdapClient\Response\Data\ObjectClassName;

class DomainDefinitionObjectClassName extends AbstractObjectiveClassNameDataDefinition
{
    public function getObjectClassName(): ObjectClassName
    {
        return $this->values['domain'] ??= new ObjectClassName('domain');
    }
}
