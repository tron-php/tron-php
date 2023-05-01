<?php

namespace TronPHP\Currencies;

use Brick\Money\Currency;
use Brick\Money\Money;
use NumberFormatter;

class TRX extends CryptoCurrency
{
    public function currency(): Currency
    {
        return new Currency('TRX', 0, 'TRX', 6);
    }

    public function formatter(): NumberFormatter
    {
        $formatter = new NumberFormatter(null, NumberFormatter::CURRENCY);

        $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, 'TRX');
        $formatter->setPattern('#,##0.000000 ¤');

        return $formatter;
    }

    public function qrCodeUrl(string $address, Money $money): string
    {
        $money = CryptoCurrency::converter()->convert($money, $this->currency());

        return "tron:{$address}?amount={$money->getAmount()}";
    }
}
