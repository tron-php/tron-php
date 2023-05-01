<?php

namespace TronPHP\Currencies;

use Brick\Money\Currency;
use Brick\Money\Money;
use NumberFormatter;

class USDT extends CryptoCurrency
{
    public function currency(): Currency
    {
        return new Currency('USDT', 0, 'USDTTRC20', 6);
    }

    public function formatter(): NumberFormatter
    {
        $formatter = new NumberFormatter(null, NumberFormatter::CURRENCY);

        $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, 'USDTTRC20');
        $formatter->setPattern('#,##0.000000 ¤');

        return $formatter;
    }

    public function qrCodeUrl(string $address, Money $money): string
    {
        $money = CryptoCurrency::converter()->convert($money, $this->currency());

        return "tether-trc-20:{$address}?amount={$money->getAmount()}";
    }
}
