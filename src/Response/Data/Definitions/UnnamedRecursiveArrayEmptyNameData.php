<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data\Definitions;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataStringableEmptyNameInterface;
use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractRdapResponseDataRecursiveArrayEmptyName;
use ArrayAccess\RdapClient\Response\Data\NonStandards\UnnamedStringData;
use Stringable;

class UnnamedRecursiveArrayEmptyNameData extends AbstractRdapResponseDataRecursiveArrayEmptyName
{
    public function __construct(string|Stringable ...$data)
    {
        $this->values = [];
        foreach ($data as $item) {
            if ($item instanceof RdapResponseDataStringableEmptyNameInterface) {
                $this->values[] = $item;
                continue;
            }
            $this->values[] = new UnnamedStringData($item);
        }
    }

    /**
     * @return array<UnnamedStringData|RdapResponseDataStringableEmptyNameInterface>
     */
    public function getValues(): array
    {
        return parent::getValues();
    }
}
