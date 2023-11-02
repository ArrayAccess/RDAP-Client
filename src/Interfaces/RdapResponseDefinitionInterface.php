<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces;

use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataConformanceDataInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataConformanceInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataErrorCodeInterface;
use ArrayAccess\RdapClient\Interfaces\ResponseData\RdapResponseDataObjectDataClassNameInterface;
use JsonSerializable;
use Stringable;

interface RdapResponseDefinitionInterface extends Stringable, JsonSerializable
{
    public function getRdapConformance(): ?RdapResponseDataConformanceInterface;

    public function getRdapConformanceData(): ?RdapResponseDataConformanceDataInterface;

    public function getObjectClassName(): ?RdapResponseDataObjectDataClassNameInterface;

    public function getErrorCode(): ?RdapResponseDataErrorCodeInterface;

    public function getRelatedRequest(): ?RdapRequestInterface;
}
