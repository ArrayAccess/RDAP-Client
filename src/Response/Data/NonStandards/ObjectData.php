<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\NonStandards;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataNamedInterface;
use ArrayAccess\RdapClient\Response\Traits\AllowedKeyDataTraits;
use stdClass;
use function get_object_vars;

class ObjectData implements RdapResponseDataInterface
{
    use AllowedKeyDataTraits;

    protected object $values;

    public function __construct(RdapResponseDataNamedInterface ...$data)
    {
        $this->values = new stdClass();
        foreach ($data as $key => $datum) {
            $this->values->$key = $datum;
        }
    }

    public function rootOnly() : bool
    {
        return false;
    }

    public function getName(): void
    {
    }

    public function getValues(): object
    {
        return $this->values;
    }

    public function jsonSerialize(): object
    {
        return $this->getValues();
    }

    public function getPlainData(): object
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
