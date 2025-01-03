<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArray;

class SecureDNS extends AbstractRdapResponseDataRecursiveArray
{
    /**
     * @var string $name
     */
    protected string $name = 'secureDNS';

    /**
     * @var string[] $allowedKeys
     */
    protected array $allowedKeys = [
        'delegationSigned',
        'ZoneSigned'
    ];

    /**
     * Constructor
     * @param ZoneSigned|DelegationSigned|DsData ...$signed
     */
    public function __construct(
        ZoneSigned|DelegationSigned|DsData...$signed
    ) {
        $this->values = [];
        foreach ($signed as $sign) {
            $this->values[$sign->getName()] = $sign;
        }
    }
}
