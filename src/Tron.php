<?php

namespace TronPHP;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use TronPHP\Support\Base58Check;
use TronPHP\Tron\Api;
use TronPHP\Tron\Network;

class Tron
{
    const HEX_ADDRESS_PREFIX = '41';

    const USDT_CONTRACT_ADDRESS = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';

    const ENERGY_SUN_COST = 420;

    const BANDWIDTH_SUN_COST = 1000;

    const USDT_TRANSFER_BANDWIDTH = 345;

    public function __construct(protected string $apiKey) {}

    public function api(
        string $path,
        array $data = [],
        string $method = 'get',
        Network $network = null,
        Api $api = Api::FULL_NODE,
    ): array
    {
        $path = Str::start($path, '/');

        return $this->http($api, $network)->{$method}($path, $data)->throw()->json();
    }

    public function tronGridApi(
        string $path,
        array $data = [],
        string $method = 'get',
        Network $network = null,
    ): array
    {
        return $this->api($path, $data, $method, $network, Api::TRONGRID);
    }

    protected function http(Api $api, Network $network = null): PendingRequest
    {
        if (! $network) {
            $network = Network::MAINNET;
        }

        $baseUrl = $network->url() . $api->path();

        $http = Http::baseUrl($baseUrl);

        if ($network === Network::MAINNET) {
            $http = $http->withHeaders(['TRON-PRO-API-KEY' => $this->apiKey]);
        }

        return $http;
    }

    public static function base58ToHex(string $base58Address): string
    {
        return Base58Check::decode($base58Address, 0, 3);
    }

    public static function hexToBase58(string $hexAddress): string
    {
        return Base58Check::encode($hexAddress, 0, false);
    }
}
