<?php

namespace TronPHP\Actions;

use Elliptic\EC;
use TronPHP\Support\Base58;
use TronPHP\Support\Crypto;
use TronPHP\Support\Keccak;
use TronPHP\Tron;
use TronPHP\TronAccount;

class GenerateTronAccount extends Action
{
    public function handle(): TronAccount
    {
        $ec = new EC('secp256k1');

        $key = $ec->genKeyPair();
        $privateKey = $ec->keyFromPrivate($key->getPrivate());
        $publicKeyHex = $privateKey->getPublic(enc: 'hex');

        $publicKeyBin = hex2bin($publicKeyHex);
        $addressHex = static::getAddressHex($publicKeyBin);
        $addressBin = hex2bin($addressHex);
        $addressBase58 = static::getBase58CheckAddress($addressBin);

        return new TronAccount($addressBase58, $privateKey->getPrivate('hex'));
    }

    protected function getAddressHex(string $publicKeyBin): string
    {
        if (strlen($publicKeyBin) == 65) {
            $publicKeyBin = substr($publicKeyBin, 1);
        }

        $hash = Keccak::hash($publicKeyBin, 256);

        return Tron::HEX_ADDRESS_PREFIX . substr($hash, 24);
    }

    protected function getBase58CheckAddress(string $addressBin): string
    {
        $hash0 = hash('sha256', $addressBin, true);
        $hash1 = hash('sha256', $hash0, true);
        $checksum = substr($hash1, 0, 4);
        $checksum = $addressBin . $checksum;

        return Base58::encode(Crypto::bin2bc($checksum));
    }
}
