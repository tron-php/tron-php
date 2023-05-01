<?php

namespace TronPHP;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class TronServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(Tron::class, fn () =>
            new Tron($this->app['config']->get('services.tron.api_key'))
        );
    }

    public function provides(): array
    {
        return [Tron::class];
    }
}
