<?php

namespace TronPHP;

readonly class BroadcastTransactionResponse
{
    public bool $result;

    public string $transactionId;

    public function __construct(array $data)
    {
        $this->result = $data['result'];
        $this->transactionId = $data['txid'];
    }
}
