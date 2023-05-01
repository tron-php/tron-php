<?php

namespace TronPHP\Currencies;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Brick\Money\Currency;
use Brick\Money\CurrencyConverter;
use Brick\Money\ExchangeRateProvider\ConfigurableProvider;
use Brick\Money\Money;
use NumberFormatter;

abstract class CryptoCurrency
{
    abstract public function currency(): Currency;

    abstract public function formatter(): NumberFormatter;

    abstract public function qrCodeUrl(string $address, Money $money): string;

    public static function converter(): CurrencyConverter
    {
        $exchangeRateProvider = new ConfigurableProvider;

        $exchangeRateProvider->setExchangeRate(
            CryptoCurrency\Code::SUN->value,
            CryptoCurrency\Code::TRX->value,
            0.000001,
        );

        $exchangeRateProvider->setExchangeRate(
            CryptoCurrency\Code::TRX->value,
            CryptoCurrency\Code::SUN->value,
            1000000,
        );

        return new CurrencyConverter($exchangeRateProvider);
    }

    public function qrCodeSvg(string $address, Money $money, int $width = 160): string
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle($width, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
                new SvgImageBackEnd,
            )
        ))->writeString($this->qrCodeUrl($address, $money));

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }
}
