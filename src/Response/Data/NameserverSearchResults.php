<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArray;
use ArrayAccess\RdapClient\Response\Data\Definitions\NameserverDefinitionObjectClassName;
use function array_values;

class NameserverSearchResults extends AbstractRdapResponseDataRecursiveArray
{
    /**
     * @var string $name
     */
    protected string $name = 'nameserverSearchResults';

    /**
     * @param NameserverDefinitionObjectClassName ...$data
     */
    public function __construct(NameserverDefinitionObjectClassName ...$data)
    {
        $this->values = array_values($data);
    }
}
