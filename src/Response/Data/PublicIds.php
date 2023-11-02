<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArray;
use ArrayAccess\RdapClient\Response\Data\Definitions\PublicIdsDefinitions;
use function array_values;

class PublicIds extends AbstractRdapResponseDataRecursiveArray
{
    protected string $name = 'publicIds';

    protected ?array $allowedKeys = null;

    public function __construct(PublicIdsDefinitions ...$data)
    {
        $this->values = array_values($data);
    }
}
