<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

interface RdapResponseDataErrorCodeInterface extends RdapResponseDataIntegerInterface
{
    public function getName(): string;

    public function isValid(): bool;
}
