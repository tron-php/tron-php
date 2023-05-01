<?php

namespace TronPHP\Tron;

enum Api
{
    case TRONGRID;
    case FULL_NODE;

    public function path(): string
    {
        return match ($this) {
            Api::FULL_NODE => '',
            Api::TRONGRID => '/v1',
        };
    }
}
