<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArray;

class SecureDNS extends AbstractRdapResponseDataRecursiveArray
{
    protected string $name = 'secureDNS';

    protected array $allowedKeys = [
        'delegationSigned',
        'ZoneSigned'
    ];

    public function __construct(
        ZoneSigned|DelegationSigned|DsData...$signed
    ) {
        $this->values = [];
        foreach ($signed as $sign) {
            $this->values[$sign->getName()] = $sign;
        }
    }
}
