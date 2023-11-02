<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArray;

class IpAddresses extends AbstractRdapResponseDataRecursiveArray
{
    protected string $name = 'ipAddresses';

    public function __construct(Ipv4|Ipv6 ...$data)
    {
        $this->values = [];
        foreach ($data as $item) {
            $this->values[$item->getName()] = $item;
        }
    }
}
