<?php

namespace TronPHP;

use Brick\Money\Money;
use TronPHP\Currencies\CryptoCurrency;

readonly class UsdtTransaction
{
    public string $id;

    public Money $money;

    public string $fromAddress;

    public string $toAddress;

    public int $timestamp;

    public function __construct(array $data)
    {
        $this->id = $data['transaction_id'];
        $this->money = Money::ofMinor($data['value'], CryptoCurrency\Code::USDT->currency());
        $this->fromAddress = $data['from'];
        $this->toAddress = $data['to'];
        $this->timestamp = $data['block_timestamp'];
    }
}
