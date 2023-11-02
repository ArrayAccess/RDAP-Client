<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Response\Data;

use ArrayAccess\RdapClient\Response\Data\Abstracts\AbstractBooleanData;

class ZoneSigned extends AbstractBooleanData
{
    protected string $name = 'zoneSigned';

    public function isSigned() : bool
    {
        return $this->values;
    }
}
