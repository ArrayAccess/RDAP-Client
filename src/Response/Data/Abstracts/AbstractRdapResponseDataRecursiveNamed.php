<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Abstracts;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataRecursiveNamedInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;

abstract class AbstractRdapResponseDataRecursiveNamed implements RdapResponseDataRecursiveNamedInterface
{
    use AllowedKeyDataTraits;

    protected string $name;

    protected RdapResponseDataInterface $values;

    public function rootOnly() : bool
    {
        return false;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValues(): RdapResponseDataInterface
    {
        return $this->values;
    }

    public function jsonSerialize() : RdapResponseDataInterface
    {
        return $this->getValues();
    }

    public function getPlainData()
    {
        return $this->getValues()->getPlainData();
    }
}
