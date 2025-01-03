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

    /**
     * @var string $name The name of the class
     */
    protected string $name;

    /**
     * @var string|Stringable|null $values The values of the class
     */
    protected string|Stringable|null $values;

    /**
     * @var bool $nullable The nullable of the class
     */
    protected bool $nullable = true;

    /**
     * @inheritDoc
     */
    public function rootOnly() : bool
    {
        return false;
    }

    /**
     * @return string The name of the class
     */
    public function getName(): string
    {
        if (!isset($this->name)) {
            $className = explode('\\', $this::class);
            $this->name = ucfirst(end($className));
        }
        return $this->name;
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
     * @return ?string
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
        return $this->nullable;
    }

    /**
     * @inheritDoc
     * @return Stringable|string|null
     */
    public function jsonSerialize(): Stringable|string|null
    {
        return $this->getStringData();
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getStringData();
    }

    /**
     * @return mixed
     */
    public function getPlainData() : mixed
    {
        return $this->values instanceof RdapResponseDataInterface
            ? $this->values->getPlainData()
            : $this->values;
    }
}
