<?php

namespace TronPHP\Support;

class Base58
{
    public static function encode(string $number, int $length = 58): string
    {
        return Crypto::dec2base($number, $length, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
    }

    public static function decode(string $address, int $length = 58): string
    {
        return Crypto::base2dec($address, $length, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
    }
}
