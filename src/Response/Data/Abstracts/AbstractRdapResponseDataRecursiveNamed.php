<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Abstracts;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataRecursiveNamedInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;

abstract class AbstractRdapResponseDataRecursiveNamed implements RdapResponseDataRecursiveNamedInterface
{
    use AllowedKeyDataTraits;

    /**
     * @var string $name Response name
     */
    protected string $name;

    /**
     * @var RdapResponseDataInterface $values Response data
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getValues(): RdapResponseDataInterface
    {
        return $this->values;
    }

    /**
     * @return RdapResponseDataInterface
     */
    public function jsonSerialize() : RdapResponseDataInterface
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
