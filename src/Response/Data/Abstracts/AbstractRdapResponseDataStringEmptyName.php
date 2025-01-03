<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Abstracts;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataStringableEmptyNameInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;
use Stringable;

abstract class AbstractRdapResponseDataStringEmptyName implements RdapResponseDataStringableEmptyNameInterface
{
    use AllowedKeyDataTraits;

    /**
     * @var string|Stringable|null $values
     */
    protected string|Stringable|null $values = null;

    /**
     * @inheritDoc
     */
    final public function getName(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function rootOnly() : bool
    {
        return false;
    }

    /**
     * @return string|Stringable|null
     */
    public function getValues(): string|Stringable|null
    {
        return $this->values;
    }

    /**
     * @inheritDoc
     */
    public function getStringData(): ?string
    {
        $data = $this->getValues();
        return $data === null && $this->isNullable()
            ? $data
            : (string) $data;
    }

    /**
     * @return bool
     */
    public function isNullable() : bool
    {
        return true;
    }

    /**
     * @inheritDoc
     * @return string|null
     */
    public function jsonSerialize(): ?string
    {
        return $this->getStringData();
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->values;
    }

    /**
     * @inheritDoc
     * @return mixed
     */
    public function getPlainData() : mixed
    {
        return $this->values instanceof RdapResponseDataInterface
            ? $this->values->getPlainData()
            : $this->values;
    }
}
