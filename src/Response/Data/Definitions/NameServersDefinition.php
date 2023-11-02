<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Definitions;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataNamedInterface;
use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArrayEmptyName;
use ArrayAccess\RdapClient\Response\Data\IpAddresses;
use ArrayAccess\RdapClient\Response\Data\LdhName;
use ArrayAccess\RdapClient\Response\Data\ObjectClassName;

class NameServersDefinition extends AbstractRdapResponseDataRecursiveArrayEmptyName
{
    public function __construct(
        RdapResponseDataNamedInterface ...$values
    ) {
        foreach ($values as $value) {
            $this->values[$value->getName()] = $value;
        }
        $this->values['objectClassName'] ??= new ObjectClassName('nameserver');
    }

    public function getLdhName() : ?LdhName
    {
        return $this->values['ldhName']??null;
    }

    public function getObjectClassName() : ObjectClassName
    {
        return $this->values['objectClassName'];
    }

    public function getIpAddresses() : ?IpAddresses
    {
        return $this->values['ipAddresses']??null;
    }
}
