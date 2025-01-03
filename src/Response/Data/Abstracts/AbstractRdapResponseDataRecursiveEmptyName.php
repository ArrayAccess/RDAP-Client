<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Abstracts;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataRecursiveEmptyNameInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;

abstract class AbstractRdapResponseDataRecursiveEmptyName implements RdapResponseDataRecursiveEmptyNameInterface
{
    use AllowedKeyDataTraits;

    /**
     * @var RdapResponseDataInterface $values
     */
    protected RdapResponseDataInterface $values;

    /**
     * @inheritDoc
     */
    public function rootOnly() : bool
    {
        return false;
    }


    /**
     * @inheritDoc
     */
    final public function getName(): void
    {
    }


    /**
     * @inheritDoc
     */
    public function getValues(): RdapResponseDataInterface
    {
        return $this->values;
    }


    /**
     * @inheritDoc
     */
    public function jsonSerialize(): RdapResponseDataInterface
    {
        return $this->getValues();
    }

    /**
     * @inheritDoc
     */
    public function getPlainData() : mixed
    {
        return $this->getValues()->getPlainData();
    }
}
