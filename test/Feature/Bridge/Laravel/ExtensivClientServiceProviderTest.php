<?php
/*
 * Copyright (c) 2023 GT Logistics.
 */

namespace Gtlogistics\QuickbaseClient\Test\Feature\Bridge\Laravel;

use Gtlogistics\ExtensivClient\Bridge\Laravel\ExtensivClientServiceProvider;
use Gtlogistics\ExtensivClient\ExtensivClient;
use Lcobucci\Clock\FrozenClock;
use Nyholm\Psr7\Factory\Psr17Factory;
use Orchestra\Testbench\TestCase;
use Psr\Clock\ClockInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Psr18Client;

class ExtensivClientServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        $this->afterApplicationCreated(function () {
            $client = new Psr18Client(new MockHttpClient());
            $psr17Factory = new Psr17Factory();

            $this->instance(ClientInterface::class, $client);
            $this->instance(RequestFactoryInterface::class, $psr17Factory);
            $this->instance(UriFactoryInterface::class, $psr17Factory);
            $this->instance(StreamFactoryInterface::class, $psr17Factory);
            $this->mock(ClockInterface::class);
        });

        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            ExtensivClientServiceProvider::class,
        ];
    }

    public function testRegister(): void
    {
        self::assertSame('user', config('extensiv.username'));
        self::assertSame('pass', config('extensiv.password'));
        self::assertSame('test', config('extensiv.tpl'));
        self::assertSame('https://example.com', config('extensiv.base_uri'));

        $extensivClient = $this->app->make(ExtensivClient::class);
        self::assertInstanceOf(ExtensivClient::class, $extensivClient);
    }
}
