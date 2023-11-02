<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

interface RdapResponseDataConformanceInterface extends RdapResponseDataInterface
{
    public function getName(): string;

    public function getHints(): array;

    public function containHint(string $hint): bool;

    public function containPrefixHint(string $hint): bool;
}
