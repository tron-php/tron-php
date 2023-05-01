<?php

namespace TronPHP\Currencies\CryptoCurrency;

use Brick\Money\Currency;
use TronPHP\Currencies\CryptoCurrency;

/**
 * @method Currency currency
 */
enum Code: string
{
    case SUN = 'SUN';

    case TRX = 'TRX';

    case USDT = 'USDT';

    public function init(): CryptoCurrency
    {
        $currencyClass = "\TronPHP\Currencies\\{$this->value}";

        return new $currencyClass;
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->init()->{$name}(...$arguments);
    }
}
