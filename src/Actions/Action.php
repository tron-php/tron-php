<?php

namespace TronPHP\Actions;

use Illuminate\Container\Container;

abstract class Action
{
    public static function execute(): mixed
    {
        return Container::getInstance()->make(static::class)->handle(...func_get_args());
    }
}
