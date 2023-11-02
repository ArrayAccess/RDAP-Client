<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Services;

use function explode;
use function is_numeric;
use function is_string;
use function preg_match;
use function reset;
use function str_contains;
use function strtolower;

class AsnService extends AbstractRdapService
{
    const MAX_INTEGER = 4294967296;

    protected function normalizeSource(string $target) : string
    {
        if (str_contains($target, '.')) {
            $this->throwInvalidTarget($target);
        }
        if (is_numeric($target) && !str_contains($target, '.')) {
            return $target;
        }

        $explode = explode('-', $target);
        // xx-xxx
        if (count($explode) !== 2) {
            $this->throwInvalidTarget($target);
        }
        foreach ($explode as $item) {
            if (str_contains($item, '.')
                || !is_numeric($item)
                || ((int)$item) < 0
                || ((int)$item) > self::MAX_INTEGER
            ) {
                $this->throwInvalidTarget($target);
            }
        }
        return $target;
    }

    public function normalize(string $target) : ?string
    {
        $target = trim($target);
        if ($target === '') {
            return null;
        }
        $target = strtolower($target);
        if (!preg_match('~^(?:asn?)?([0-9]+)$~', $target, $match)) {
            return null;
        }
        $integer = ((int) ($match[1]));
        return $integer > 0 && $integer <= self::MAX_INTEGER ? $match[1] : null;
    }

    public function getRdapURL(string|int $target): ?string
    {
        $target = $this->normalize((string) $target);
        if ($target === null) {
            return null;
        }
        if ($target < 0 || $target > self::MAX_INTEGER) {
            return null;
        }
        foreach ($this->services as $service) {
            $urls = $service[1]??[];
            $url = reset($urls);
            if (!$url) {
                continue;
            }
            foreach ($service[0] as $number) {
                if (!is_string($number)) {
                    continue;
                }
                if (!str_contains($number, '-')) {
                    if (!is_numeric($number)) {
                        continue;
                    }
                    if ($target === $number) {
                        return $url;
                    }
                    continue;
                }
                [$start, $end] = explode('-', $number);
                if ($start <= $target && $end >= $target) {
                    return $url;
                }
            }
        }
        return null;
    }
}
