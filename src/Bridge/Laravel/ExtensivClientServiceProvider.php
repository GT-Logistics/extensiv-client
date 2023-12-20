<?php
/*
 * Copyright (c) 2023 GT Logistics.
 */

namespace Gtlogistics\ExtensivClient\Bridge\Laravel;

use Gtlogistics\ExtensivClient\ExtensivClient;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Psr\Clock\ClockInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class ExtensivClientServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/config.php' => config_path('extensiv.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'extensiv');

        $this->app->singleton(ExtensivClient::class, function (Application $app) {
            return new ExtensivClient(
                $app->get(ClientInterface::class),
                $app->get(RequestFactoryInterface::class),
                $app->get(StreamFactoryInterface::class),
                $app->get(UriFactoryInterface::class),
                $app->get(ClockInterface::class),
                config('extensiv.username'),
                config('extensiv.password'),
                config('extensiv.tpl'),
                config('extensiv.base_uri'),
            );
        });
    }
}
