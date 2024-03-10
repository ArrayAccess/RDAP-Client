<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Services;

use ArrayAccess\RdapClient\Util\CIDR;
use DateTimeInterface;
use function array_merge;
use function explode;
use function is_array;
use function is_numeric;
use function preg_match;
use function str_contains;
use function str_ends_with;
use function str_starts_with;

class Ipv4Service extends AbstractRdapService
{
    private array $cidrRanges = [];

    private bool $addedRecovered = false;

    public function __construct(
        string $version,
        string $description,
        DateTimeInterface|string $publication,
        array $services,
        bool $addRecoveredIpv4 = false
    ) {
        parent::__construct($version, $description, $publication, $services);
        if (true === $addRecoveredIpv4) {
            $this->addRecoveredIpv4();
        }
    }

    protected function normalizeSource(string $target): string
    {
        // 255.255.255.255 -> 15 chars
        // 1.1.1.1 -> 7 chars
        $explode = explode('/', $target);
        if (count($explode) === 2
            && is_numeric($explode[1])
            && !str_contains($explode[1], '.')
            && ((int) $explode[1]) >= 0
            && ((int) $explode[1]) <= 32
            && str_contains($explode[0], '.')
            && strlen($explode[0]) <= 15
            && strlen($explode[0]) >= 7
            && ($_target = $this->normalize($explode[0]))
        ) {
            return "$_target/$explode[1]";
        }
        $this->throwInvalidTarget($target);
    }

    public function addRecoveredIpv4(): static
    {
        if ($this->addedRecovered) {
            return $this;
        }
        $this->addedRecovered = true;
        foreach (RecoveredIPv4::RECOVERED_IPS as $target => $ips) {
            $offset = $this->getOffset($target);
            if ($offset === null) {
                continue;
            }
            $this->services[$offset][0] = array_merge($ips, $this->services[$offset][0]);
        }
        return $this;
    }

    public function getRdapURL(string $target) : ?string
    {
        $target = $this->normalize($target);
        if (!$target) {
            return null;
        }
        [$start, $second] = explode('.', $target);
        $start .= '.';
        $second = "$start$second.";
        foreach ($this->services as $service) {
            $urls = $service[1]??[];
            $url = reset($urls);
            if (!$url) {
                continue;
            }
            foreach ($service[0] as $cidr) {
                if (!str_starts_with($cidr, $start)) {
                    continue;
                }
                if (str_ends_with($cidr, '.0.0.0/8')) {
                    return $url;
                }
                if (!str_starts_with($cidr, $second)) {
                    continue;
                }
                if (!isset($this->cidrRanges[$cidr])) {
                    $this->cidrRanges[$cidr] = CIDR::cidrToRange($cidr);
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

    public function normalize(string $target): ?string
    {
        if (str_contains($target, '/')) {
            return $this->normalizeSource($target);
        }
        if (!preg_match(
            '~^(?:[01]?[0-9]{1,2}|2[0-4][0-9]|25[0-5])(?:\.(?:[01]?[0-9]{1,2}|2[0-4][0-9]|25[0-5])){3}$~x',
            $target
        )) {
            return null;
        }
        $target = CIDR::filter($target);
        if (!$target) {
            return null;
        }
        return $target;
    }
}
