<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Services;

use ArrayAccess\RdapClient\Util\CIDR;
use function explode;
use function is_array;
use function is_numeric;
use function reset;
use function str_contains;
use function str_starts_with;

class Ipv6Service extends AbstractRdapService
{
    /**
     * @var array<string, array{0: string, 1: string}|false> $cidrRanges The RDAP services
     */
    private array $cidrRanges = [];

    /**
     * @inheritDoc
     */
    protected function normalizeSource(string $target): string
    {
        $explode = explode('/', $target);
        if (count($explode) === 2
            && is_numeric($explode[1])
            && !str_contains($explode[1], '.')
            && ((int) $explode[1]) >= 0
            && ((int) $explode[1]) <= 128
            && ($_target = $this->normalize($explode[0])) !== null
        ) {
            return "$_target/$explode[1]";
        }

        $this->throwInvalidTarget($target);
    }

    /**
     * @inheritDoc
     */
    public function normalize(string $target): ?string
    {
        if (!str_contains($target, ':')) {
            return null;
        }
        if (str_contains($target, '/')) {
            return $this->normalizeSource($target);
        }
        $target = CIDR::filter($target);
        if (!$target) {
            return null;
        }
        return $target;
    }

    /**
     * @inheritDoc
     */
    public function getRdapURL(string $target) : ?string
    {
        $target = $this->normalize($target);
        if (!$target) {
            return null;
        }
        [$start] = explode(':', $target);
        // just take first character
        // @previous: $start += ':';
        // @patch
        $start = $start[0];
        foreach ($this->services as $service) {
            $urls = $service[1]??[];
            $service0 = $service[0]??[];
            $url = reset($urls);
            if (!$url || empty($service0)) {
                continue;
            }
            foreach ($service0 as $cidr) {
                if (!str_starts_with($cidr, $start)) {
                    continue;
                }
                if (!isset($this->cidrRanges[$cidr])) {
                    $this->cidrRanges[$cidr] = CIDR::ip6cidrToRange($cidr)?:false;
                }
                if (!is_array($this->cidrRanges[$cidr])) {
                    continue;
                }
                if (CIDR::inRange($target, $this->cidrRanges[$cidr][0], $this->cidrRanges[$cidr][1])) {
                    return $url;
                }
            }
        }
        return null;
    }
}
