<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Util;

use function bin2hex;
use function dechex;
use function explode;
use function hexdec;
use function implode;
use function inet_ntop;
use function inet_pton;
use function ip2long;
use function is_numeric;
use function long2ip;
use function min;
use function pow;
use function preg_match;
use function str_contains;
use function str_split;
use function substr;
use function substr_replace;
use function trim;

class CIDR
{
    public static function normalizeIp6(string $ip) :?string
    {
        if (!str_contains($ip, ':')) {
            return null;
        }
        if (($bin = inet_pton($ip)) === false) {
            return null;
        }
        return implode(':', str_split(bin2hex($bin), 4));
    }

    public static function normalizeIp4(string $arg): ?string
    {
        $arg = trim($arg);
        if ($arg === '') {
            return null;
        }
        $ips = explode('.', $arg, 4);
        foreach ($ips as $k => $ip) {
            if ($ip === '') {
                $ips[$k] = 0;
                continue;
            }
            if ($ip === '0') {
                continue;
            }
            if (str_contains($ip, '.')
                || ! is_numeric($ip)
                || ($ip = (int) $ip) < 0
                || $ip > 255
            ) {
                return null;
            }
            $ips[$k] = $ip;
        }
        while (count($ips) < 4) {
            $ips[] = "0";
        }
        return implode(".", $ips);
    }

    /**
     * @param string $cidr
     * @return ?array{0:string, 1:string}
     */
    public static function ip4CidrToRange(string $cidr): ?array
    {
        if (count(($cidr = explode('/', trim($cidr)))) !== 2) {
            return null;
        }
        $ip    = trim($cidr[0]);
        $range = trim($cidr[1]);
        if ($ip === ''
            || $range === ''
            || !is_numeric($range)
            || str_contains($range, '.')
            || $range > 32
            || $range < 0
            || !($ip = self::normalizeIp4($ip))
        ) {
            return null;
        }
        $range = (int) $range;
        return [
            long2ip((ip2long($ip)) & ((-1 << (32 - $range)))),
            long2ip((ip2long($ip)) + pow(2, (32 - $range)) - 1)
        ];
    }

    /**
     * @param string $cidr
     * @return ?array{0:string, 1:string}
     */
    public static function ip6cidrToRange(string $cidr) : ?array
    {
        if (count(($cidr = explode('/', trim($cidr)))) !== 2) {
            return null;
        }
        $ip    = trim($cidr[0]);
        $range = trim($cidr[1]);
        if ($ip === ''
            || $range === ''
            || str_contains($range, '.')
            || !is_numeric($range)
            || $range < 0
            || $range > 128
            || !($firstAddr = self::normalizeIp6($ip))
        ) {
            return null;
        }
        $firstAddrBin = inet_pton($firstAddr);
        // fail return null
        if ($firstAddrBin === false) {
            return null;
        }
        $flexBits = 128 - ((int) $range);
        // Build the hexadecimal string of the last address
        $lastAddrHex = bin2hex($firstAddrBin);
        // start at the end of the string (which is always 32 characters long)
        $pos = 31;
        while ($flexBits > 0) {
            // Get the character at this position
            $orig = substr($lastAddrHex, $pos, 1);
            // Convert it to an integer
            $originalVal = hexdec($orig);
            // OR it with (2^flexBits)-1, with flexBits limited to 4 at a time
            $newVal = $originalVal | (pow(2, min(4, $flexBits)) - 1);
            // Convert it back to a hexadecimal character
            $new = dechex($newVal);
            // And put that character back in the string
            $lastAddrHex = substr_replace($lastAddrHex, $new, $pos, 1);
            // process one nibble, move to previous position
            $flexBits -= 4;
            $pos -= 1;
        }

        $lastAddr = implode(':', str_split($lastAddrHex, 4));
        return [$firstAddr, $lastAddr];
    }

    /**
     * @param string $cidr
     * @return ?array{0:string, 1:string}
     */
    public static function cidrToRange(string $cidr) : ?array
    {
        if (str_contains($cidr, ':')) {
            return self::ip6cidrToRange($cidr);
        }
        return self::ip4cidrToRange($cidr);
    }

    public static function normalize(string $ip): ?string
    {
        $ip = trim($ip);
        if ($ip === '') {
            return null;
        }
        if (str_contains($ip, ':') || preg_match('~[a-f]~', $ip)) {
            return self::normalizeIp6($ip);
        }
        return self::normalizeIp4($ip);
    }

    public static function filterIp6(string $ip): ?string
    {
        $ip = trim($ip);
        if ($ip === '' || !str_contains($ip, ':')) {
            return null;
        }
        $bin = inet_pton($ip);
        return $bin !== false ? inet_ntop($bin) : null;
    }

    public static function filterIp4(string $ip): ?string
    {
        $ip = trim($ip);
        if ($ip === '' || !str_contains($ip, '.')) {
            return null;
        }
        $bin = ip2long($ip);
        return $bin !== false ? long2ip($bin) : null;
    }

    public static function filter(string $ip): ?string
    {
        return self::filterIp6($ip)??self::filterIp4($ip);
    }

    /**
     * @param string $ip
     * @param string $startIP
     * @param string $endIP
     * @return bool
     */
    public static function inRange(string $ip, string $startIP, string $endIP): bool
    {
        $ip = inet_pton($ip);
        $startIP = $ip !== false ? inet_pton($startIP) : false;
        $endIP = $startIP !== false ? inet_pton($endIP) : false;
        return $endIP !== false &&  ($ip >= $startIP && $ip <= $endIP);
    }

    /**
     * @param string $ip
     * @param string $cidr
     * @return bool
     */
    public static function inRangeCidr(string $ip, string $cidr): bool
    {
        $cidr = self::cidrToRange($cidr);
        if (!$cidr) {
            return false;
        }

        return self::inRange($ip, $cidr[0], $cidr[1]);
    }
}
