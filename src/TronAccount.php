<?php

namespace TronPHP;

use Brick\Money\Money;
use Elliptic\EC;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use TronPHP\Actions\GenerateTronAccount;
use TronPHP\Currencies\CryptoCurrency;
use TronPHP\UsdtTransaction\Filter;
use TronPHP\UsdtTransaction\Target;

readonly class TronAccount
{
    public function __construct(
        public string $address,
        public ?string $privateKey = null,
    ) {}

    public function sun(): Money
    {
        $info = $this->tronGridApi("accounts/{$this->address}");

        $sun = $info['data'][0]['balance'] ?? 0;

        return Money::of($sun, CryptoCurrency\Code::SUN->currency());
    }

    public function usdt(): Money
    {
        $requestData = $this->getRequestDataForUsdtBalance();

        $response = $this->api('wallet/triggerconstantcontract', $requestData, 'post');

        $integerAmount = hexdec($response['constant_result'][0]);

        return Money::ofMinor($integerAmount, CryptoCurrency\Code::USDT->currency());
    }

    public function transferSun(Money $money, TronAccount|string $to): BroadcastTransactionResponse
    {
        $transaction = $this->api('wallet/createtransaction', [
            'to_address' => Tron::base58ToHex($to instanceof TronAccount ? $to->address : $to),
            'owner_address' => Tron::base58ToHex($this->address),
            'amount' => $money->getAmount()->toInt(),
        ], 'post');

        return $this->broadcastTransaction($transaction);
    }

    public function transferUsdt(Money $money, TronAccount|string $to): BroadcastTransactionResponse
    {
        $requestData = $this->getRequestDataForUsdtTransfer(
            $money,
            $to instanceof TronAccount ? $to->address : $to,
        );

        $requestData['fee_limit'] = CryptoCurrency::converter()->convert(
            Money::of(100, CryptoCurrency\Code::TRX->currency()),
            CryptoCurrency\Code::SUN->currency(),
        )->getAmount()->toInt();

        $response = $this->api('wallet/triggersmartcontract', $requestData, 'post');

        return $this->broadcastTransaction($response['transaction']);
    }

    public function requiredSunForUsdtTransfer(Money $money, TronAccount|string $to): Money
    {
        $requestData = $this->getRequestDataForUsdtTransfer(
            $money,
            $to instanceof TronAccount ? $to->address : $to,
        );

        $response = $this->api('wallet/triggerconstantcontract', $requestData, 'post');

        return Money::of(Tron::ENERGY_SUN_COST, CryptoCurrency\Code::SUN->currency())
            ->multipliedBy($response['energy_used']);
    }

    public function usdtTransactions(
        Filter $filter = Filter::ALL,
        Target $target = Target::ALL,
    ): Collection
    {
        $filter = match ($filter) {
            Filter::CONFIRMED => ['only_confirmed' => true],
            Filter::UNCONFIRMED => ['only_unconfirmed' => true],
            default => [],
        };

        $target = match ($target) {
            Target::ONLY_TO => ['only_to' => true],
            Target::ONLY_FROM => ['only_from' => true],
            default => [],
        };

        $contractTransactions = $this->tronGridApi("accounts/{$this->address}/transactions/trc20", array_merge($filter, $target));

        return collect($contractTransactions['data'] ?? [])
            ->filter(fn (array $contractTransaction) =>
                $contractTransaction['token_info']['symbol'] === 'USDT'
            )
            ->mapInto(UsdtTransaction::class);
    }

    public function tronClient(): Tron
    {
        return Container::getInstance()->make(Tron::class);
    }

    public function api(): array
    {
        return $this->tronClient()->api(...func_get_args());
    }

    public function tronGridApi(): array
    {
        return $this->tronClient()->tronGridApi(...func_get_args());
    }

    protected function getRequestDataForUsdtBalance(): array
    {
        return [
            'owner_address' => Tron::base58ToHex($this->address),
            'contract_address' => Tron::base58ToHex(Tron::USDT_CONTRACT_ADDRESS),
            'function_selector' => 'balanceOf(address)',
            'parameter' => str(Tron::base58ToHex($this->address))->substr(2)->padLeft(64, '0'),
        ];
    }

    protected function getRequestDataForUsdtTransfer(Money $money, TronAccount|string $to): array
    {
        $hexToAddress = Tron::base58ToHex($to instanceof TronAccount ? $to->address : $to);
        $hexMinorAmount = dechex($money->getMinorAmount()->toInt());

        return [
            'owner_address' => Tron::base58ToHex($this->address),
            'contract_address' => Tron::base58ToHex(Tron::USDT_CONTRACT_ADDRESS),
            'function_selector' => 'transfer(address,uint256)',
            'parameter' => (
                str($hexToAddress)->substr(2)->padLeft(64, '0') .
                str($hexMinorAmount)->padLeft(64, '0')
            ),
        ];
    }

    protected function signTransaction(array $transaction): array
    {
        $key = (new EC('secp256k1'))->keyFromPrivate($this->privateKey);

        $signature = $key->sign($transaction['txID']);

        $transaction['signature'] = $signature->r->bi->toHex() . $signature->s->bi->toHex() . bin2hex(chr($signature->recoveryParam));

        return $transaction;
    }

    protected function broadcastTransaction(array $transaction): BroadcastTransactionResponse
    {
        $response = $this->api(
            'wallet/broadcasttransaction',
            $this->signTransaction($transaction),
            'post',
        );

        return new BroadcastTransactionResponse($response);
    }

    public static function generate(): static
    {
        return GenerateTronAccount::execute();
    }

    public static function make(string $address, ?string $privateKey = null): static
    {
        return new static($address, $privateKey);
    }
}
