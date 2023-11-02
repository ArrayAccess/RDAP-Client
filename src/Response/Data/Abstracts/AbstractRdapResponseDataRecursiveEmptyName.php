<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Abstracts;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataRecursiveEmptyNameInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;

abstract class AbstractRdapResponseDataRecursiveEmptyName implements RdapResponseDataRecursiveEmptyNameInterface
{
    use AllowedKeyDataTraits;

    protected RdapResponseDataInterface $values;

    public function rootOnly() : bool
    {
        return false;
    }

    final public function getName(): void
    {
    }

    public function getValues(): RdapResponseDataInterface
    {
        return $this->values;
    }

    public function jsonSerialize(): RdapResponseDataInterface
    {
        return $this->getValues();
    }

    public function getPlainData()
    {
        return $this->getValues()->getPlainData();
    }
}
