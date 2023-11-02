<?php
declare(strict_types=1);

namespace ArrayAccess\RdapClient\Util;

use function array_map;
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
use function str_replace;
use function str_split;
use function strlen;
use function substr;
use function substr_replace;
use function trim;

class CIDR
{
    public static function normalizeIp6(string $ip) :?string
    {
        if (!preg_match('~^[a-f0-9]{0,4}(?::[a-f0-9]{0,4}){0,7}$~', $ip)) {
            return null;
        }

        $bin  = inet_pton($ip);
        if ($bin === false) {
            return null;
        }
        return implode(':', str_split(bin2hex($bin), 4));
    }

    public static function normalizeIp4(string $arg): ?string
    {
        $ips = explode('.', $arg);
        if (count($ips) > 4) {
            return null;
        }

        while (count($ips) < 4) {
            $ips[] = "0";
        }
        $ips = array_map(static fn($i) => $i === '' ? "0" : $i, $ips);
        $arg = implode(".", $ips);
        $length  = strlen($arg);
        if ($length < 7 // 0.0.0.0 -> 7
            || $length > 15 // 255.255.255.255 -> 15
        ) {
            return null;
        }
        return self::filter($arg);
    }

    /**
     * @param string $cidr
     * @return ?array{0:string, 1:string}
     */
    public static function ip4CidrToRange(string $cidr): ?array
    {
        $cidr = trim($cidr);
        if ($cidr === '') {
            return null;
        }
        // remove whitespace
        $cidr = str_replace(' ', '', $cidr);
        $cidr = explode('/', $cidr);
        if (count($cidr) !== 2) {
            return null;
        }

        $ip = $cidr[0];
        $range = $cidr[1];
        if (!is_numeric($range)
            || strlen($range) > 2
            || str_contains($range, '.')
            || !($ip = self::normalizeIp4($ip))
        ) {
            return null;
        }
        $range = (int) $range;
        if ($range < 0 || $range > 32) {
            return null;
        }
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
        $cidr = trim($cidr);
        if ($cidr === '') {
            return null;
        }

        // remove whitespace
        $cidr = str_replace(' ', '', $cidr);
        $cidr = explode('/', $cidr);
        if (count($cidr) !== 2) {
            return null;
        }

        // Split in address and prefix length
        [$firstAddr, $range] = $cidr;
        if (!is_numeric($range)
            || strlen($range) > 2
            || str_contains($range, '.')
            || !($firstAddr = self::normalizeIp6($firstAddr))
        ) {
            return null;
        }

        $range = (int) $range;
        if ($range < 0 || $range > 128) {
            return null;
        }
        $firstAddrBin = inet_pton($firstAddr);
        // fail return null
        if ($firstAddrBin === false) {
            return null;
        }
        $flexBits = 128 - $range;
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
