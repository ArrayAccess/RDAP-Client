<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArray;
use ArrayAccess\RdapClient\Response\Data\Definitions\NetworksDefinition;
use function array_values;

class Networks extends AbstractRdapResponseDataRecursiveArray
{
    /**
     * @var string $name The name of the object
     */
    protected string $name = 'networks';

    /**
     * @param NetworksDefinition ...$data
     */
    public function __construct(NetworksDefinition ...$data)
    {
        $this->values = array_values($data);
    }
}
