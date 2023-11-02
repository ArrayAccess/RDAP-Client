<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Abstracts;

use ArrayAccess\RdapClient\Interfaces\RdapServiceInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataStringableInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Stringable;

abstract class AbstractDateData implements RdapResponseDataStringableInterface
{
    use AllowedKeyDataTraits;

    protected string $name;

    protected DateTimeInterface $values;

    public function __construct(string|Stringable|DateTimeInterface $data)
    {
        if (!$data instanceof DateTimeInterface) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $data = new DateTimeImmutable($data);
        }
        if (!$data instanceof DateTimeImmutable) {
            $data = DateTimeImmutable::createFromInterface($data);
        }
        $data = $data->setTimezone(new DateTimeZone('Z'));
        $this->values = $data;
    }

    public function rootOnly() : bool
    {
        return false;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->values;
    }

    public function getValues(): string
    {
        return $this->getStringData();
    }

    public function getStringData(): string
    {
        return $this->values->format(RdapServiceInterface::DATE_FORMAT);
    }

    public function __toString(): string
    {
        return $this->getStringData();
    }

    public function jsonSerialize(): string
    {
        return $this->getStringData();
    }

    public function getPlainData(): string
    {
        return $this->getStringData();
    }
}
