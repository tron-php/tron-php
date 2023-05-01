<?php

namespace TronPHP\Tron;

enum Network
{
    case MAINNET;
    case SHASTA_TESTNET;
    case NILE_TESTNET;

    public function url(): string
    {
        return match ($this) {
            Network::MAINNET => 'https://api.trongrid.io',
            Network::SHASTA_TESTNET => 'https://api.shasta.trongrid.io',
            Network::NILE_TESTNET => 'https://nile.trongrid.io',
        };
    }
}
