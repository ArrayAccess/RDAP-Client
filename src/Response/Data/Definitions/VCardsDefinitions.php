<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Definitions;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArrayEmptyName;

class VCardsDefinitions extends AbstractRdapResponseDataRecursiveArrayEmptyName
{
    protected ?array $allowedKeys = null;

    public function __construct(
        VCardDefinition $vCardDefinition,
        VCardDefinition ...$vCardDefinitions
    ) {
        $this->values = [$vCardDefinition];
        foreach ($vCardDefinitions as $definition) {
            $this->values[] = $definition;
        }
    }
}
