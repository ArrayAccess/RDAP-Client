<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Interfaces\ResponseData;

interface RdapResponseDataConformanceInterface extends RdapResponseDataInterface
{
    public function getName(): string;

    /**
     * @return array<array-key, mixed>
     */
    public function getHints(): array;

    /**
     * Is the hint present in the conformance data
     * @param string $hint
     * @return bool
     */
    public function containHint(string $hint): bool;

    /**
     * Check if the hint is a prefix of the hints in the conformance data
     *
     * @param string $hint
     * @return bool
     */
    public function containPrefixHint(string $hint): bool;
}
