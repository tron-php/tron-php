<?php

namespace TronPHP\Currencies;

use Brick\Money\Currency;
use Brick\Money\Money;
use NumberFormatter;

class SUN extends CryptoCurrency
{
    public function currency(): Currency
    {
        return new Currency('SUN', 0, 'SUN', 0);
    }

    public function formatter(): NumberFormatter
    {
        $formatter = new NumberFormatter(null, NumberFormatter::CURRENCY);

        $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, 'SUN');
        $formatter->setPattern('#,##0 ¤');

        return $formatter;
    }

    public function qrCodeUrl(string $address, Money $money): string
    {
        $money = CryptoCurrency::converter()->convert($money, CryptoCurrency\Code::TRX->currency());

        return "tron:{$address}?amount={$money->getAmount()}";
    }
}
