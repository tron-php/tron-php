<?php

namespace TronPHP\Support;

use InvalidArgumentException;

class Crypto
{
    public static function bc2bin(string $number): string
    {
        return self::dec2base($number, 256);
    }

    public static function dec2base(string $decimal, int $base, string $digits = ''): string
    {
        if ($base < 2 || $base > 256) {
            throw new InvalidArgumentException("Invalid Base: {$base}");
        }

        bcscale(0);

        $value = '';

        if (! $digits) {
            $digits = self::digits($base);
        }

        while ($decimal > $base - 1) {
            $rest = bcmod($decimal, $base);
            $decimal = bcdiv($decimal, $base);
            $value = $digits[intval($rest)] . $value;
        }

        return $digits[intval($decimal)] . $value;
    }

    public static function base2dec(string $value, int $base, string $digits = ''): string
    {
        if ($base < 2 || $base > 256) {
            throw new InvalidArgumentException("Invalid Base: {$base}");
        }

        bcscale(0);

        if ($base < 37) {
            $value = strtolower($value);
        }

        if (! $digits) {
            $digits = self::digits($base);
        }

        $size = strlen($value);
        $dec = '0';

        for ($loop = 0; $loop < $size; $loop++) {
            $element = strpos($digits, $value[$loop]);
            $power = bcpow($base, $size - $loop - 1);
            $dec = bcadd($dec, bcmul($element, $power));
        }

        return $dec;
    }

    public static function digits(int $base): string
    {
        if ($base > 64) {
            $digits = '';

            for ($loop = 0; $loop < 256; $loop++) {
                $digits .= chr($loop);
            }
        } else {
            $digits = "0123456789abcdefghijklmnopqrstuvwxyz";
            $digits .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ-_";
        }

        return substr($digits, 0, $base);
    }

    public static function bin2bc(string $number): string
    {
        return self::base2dec($number, 256);
    }
}
