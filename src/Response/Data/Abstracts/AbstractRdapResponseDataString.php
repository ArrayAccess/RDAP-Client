<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Abstracts;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataNamedInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataStringableInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;
use Stringable;
use function explode;
use function ucfirst;

abstract class AbstractRdapResponseDataString implements
    RdapResponseDataStringableInterface,
    RdapResponseDataNamedInterface
{
    use AllowedKeyDataTraits;

    protected string $name;

    protected string|Stringable|null $values;

    protected bool $nullable = true;

    public function rootOnly() : bool
    {
        return false;
    }

    public function getName(): string
    {
        if (!isset($this->name)) {
            $className = explode('\\', $this::class);
            $this->name = ucfirst(end($className));
        }
        return $this->name;
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
        return $this->nullable;
    }

    public function jsonSerialize(): Stringable|string
    {
        return $this->getStringData();
    }

    public function __toString(): string
    {
        return (string) $this->getStringData();
    }

    public function getPlainData()
    {
        return $this->values instanceof RdapResponseDataInterface
            ? $this->values->getPlainData()
            : $this->values;
    }
}
