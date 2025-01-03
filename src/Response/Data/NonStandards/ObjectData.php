<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\NonStandards;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataNamedInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataStringableInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;
use stdClass;
use function get_object_vars;

class ObjectData implements RdapResponseDataInterface
{
    use AllowedKeyDataTraits;

    /**
     * @var stdClass $values Response data
     */
    protected stdClass $values;

    /**
     * @param RdapResponseDataNamedInterface|RdapResponseDataStringableInterface ...$data
     */
    public function __construct(
        RdapResponseDataNamedInterface|RdapResponseDataStringableInterface ...$data
    ) {
        $this->values = new stdClass();
        foreach ($data as $key => $datum) {
            $this->values->$key = $datum;
        }
    }

    /**
     * @inheritDoc
     * @return false
     */
    public function rootOnly() : bool
    {
        return false;
    }

    /**
     * @inheritDoc
     * @return void
     */
    public function getName(): void
    {
    }

    /**
     * @inheritDoc
     * @return stdClass
     */
    public function getValues(): stdClass
    {
        return $this->values;
    }

    /**
     * @inheritDoc
     * @return stdClass
     */
    public function jsonSerialize(): stdClass
    {
        return $this->getValues();
    }

    /**
     * @inheritDoc
     * @return stdClass
     */
    public function getPlainData(): stdClass
    {
        $data = new stdClass();
        foreach (get_object_vars($this->values) as $key => $item) {
            if ($item instanceof RdapResponseDataInterface) {
                $data->$key = $item->getPlainData();
            }
        }
        return $data;
    }
}
