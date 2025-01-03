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

    /**
     * @var string $name data name
     */
    protected string $name;

    /**
     * @var DateTimeInterface $values date data
     */
    protected DateTimeInterface $values;

    public function __construct(string|Stringable|DateTimeInterface $data)
    {
        if (!$data instanceof DateTimeInterface) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $data = new DateTimeImmutable((string) $data);
        }
        if (!$data instanceof DateTimeImmutable) {
            $data = DateTimeImmutable::createFromInterface($data);
        }
        $data = $data->setTimezone(new DateTimeZone('Z'));
        $this->values = $data;
    }

    /**
     * @inheritDoc
     * @return bool
     */
    public function rootOnly() : bool
    {
        return false;
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get date
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->values;
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function getValues(): string
    {
        return $this->getStringData();
    }

    /**
     * @inheritDoc
     */
    public function getStringData(): string
    {
        return $this->values->format(RdapServiceInterface::DATE_FORMAT);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getStringData();
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->getStringData();
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function getPlainData(): string
    {
        return $this->getStringData();
    }
}
