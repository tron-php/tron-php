<?php

namespace TronPHP\Support;

class Base58Check
{
    public static function encode(string $string, int $prefix = 128, bool $compressed = true): string
    {
        $string = hex2bin($string);

        if ($prefix) {
            $string = chr($prefix) . $string;
        }

        if ($compressed) {
            $string .= chr(0x01);
        }

        $string = $string . substr(hash('sha256', hash('sha256', $string, true), true), 0, 4);

        $base58 = Base58::encode(Crypto::bin2bc($string));

        for ($i = 0; $i < strlen($string); $i++) {
            if ($string[$i] != "\x00") {
                break;
            }

            $base58 = '1' . $base58;
        }

        return $base58;
    }

    public static function decode(
        string $string,
        int $removeLeadingBytes = 1,
        int $removeTrailingBytes = 4,
        bool $removeCompression = true,
    ): string
    {
        $string = bin2hex(Crypto::bc2bin(Base58::decode($string)));

        if ($removeLeadingBytes) {
            $string = substr($string, $removeLeadingBytes * 2);
        }

        if ($removeTrailingBytes) {
            $string = substr($string, 0, -($removeTrailingBytes * 2));
        }

        if ($removeCompression) {
            $string = substr($string, 0, -2);
        }

        return $string;
    }
}
