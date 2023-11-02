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

    protected string|Stringable|null $values = null;

    final public function getName(): void
    {
    }

    public function rootOnly() : bool
    {
        return false;
    }

    public function getValues(): string|Stringable|null
    {
        return $this->values;
    }

    public function getStringData(): ?string
    {
        $data = $this->getValues();
        return $data === null && $this->isNullable()
            ? $data
            : (string) $data;
    }

    protected function isNullable() : bool
    {
        return true;
    }

    public function jsonSerialize(): ?string
    {
        return $this->getStringData();
    }

    public function __toString(): string
    {
        return (string) $this->values;
    }

    public function getPlainData()
    {
        return $this->values instanceof RdapResponseDataInterface
            ? $this->values->getPlainData()
            : $this->values;
    }
}
