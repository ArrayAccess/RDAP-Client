<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Definitions;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataNamedInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataStringableInterface;
use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArrayEmptyName;

class PublicIdsDefinitions extends AbstractRdapResponseDataRecursiveArrayEmptyName
{
//    protected array $allowedKeys = [
//        'type',
//        'identifier',
//    ];

    public function __construct(
        RdapResponseDataStringableInterface|RdapResponseDataNamedInterface ...$data
    ) {
        $this->values = [
            'type' => null,
            'identifier' => null,
        ];
        foreach ($data as $action) {
            $this->values[$action->getName()] = $action;
        }
    }
}
