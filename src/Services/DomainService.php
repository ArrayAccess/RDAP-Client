<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Services;

use function explode;
use function idn_to_ascii;
use function in_array;
use function reset;
use function str_contains;
use function strlen;
use function strtolower;

class DomainService extends AbstractRdapService
{
    protected function normalizeSource(string $target): string
    {
        if (str_contains($target, '.')) {
            $this->throwInvalidTarget($target);
        }
        if (strlen($target) > 63 || strlen($target) < 1) {
            $this->throwInvalidTarget($target);
        }
        if (!idn_to_ascii($target)) {
            $this->throwInvalidTarget($target);
        }

        $target = strtolower($target);
        $ascii = idn_to_ascii($target);
        if ($ascii === false) {
            $this->throwInvalidTarget($target);
        }
        return $ascii;
    }

    public function normalize(string $target) : ?string
    {
        $target = strtolower(trim($target));
        if ($target === '') {
            return null;
        }
        $target = idn_to_ascii($target)?:null;
        if (!$target) {
            return null;
        }
        if (strlen($target) > 255) {
            return null;
        }
        foreach (explode('.', $target) as $part) {
            if ($part === '' || strlen($part) > 63) {
                return null;
            }
        }

        return $target;
    }

    public function getRdapURL(string $target): ?string
    {
        $target = $this->normalize($target);
        if (!$target) {
            return null;
        }
        $target = explode('.', $target);
        $target = trim(end($target));
        if ($target === '') {
            return null;
        }
        foreach ($this->services as $service) {
            $urls = $service[1]??[];
            $url = reset($urls);
            if (!$url) {
                continue;
            }
            if (in_array($target, $service[0])) {
                return $url;
            }
        }
        return null;
    }
}
