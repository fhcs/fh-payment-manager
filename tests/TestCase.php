<?php

namespace Fh\PaymentManager\Tests;

use Fh\PaymentManager\Facades\CustomerFactoryFacade;
use Fh\PaymentManager\Facades\InvoiceFactoryFacade;
use Fh\PaymentManager\Facades\OrderFactoryFacade;
use Fh\PaymentManager\Facades\PaymentQueryFacade;
use Fh\PaymentManager\Facades\PurchaseFacade;
use Fh\PaymentManager\PaymentServiceProvider;
use Fh\PaymentManager\Pscb\PscbServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public static function assertIsUrl($actual, string $message = '')
    {
        static::assertTrue(!!filter_var($actual, FILTER_VALIDATE_URL), $message);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withFactories(__DIR__ . '/../database/factories');
    }

    protected function getPackageProviders($app): array
    {
        return [
            PaymentServiceProvider::class,
            PscbServiceProvider::class
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'OrderFactoryFacade' => OrderFactoryFacade::class,
            'CustomerFactoryFacade' => CustomerFactoryFacade::class,
            'InvoiceFactoryFacade' => InvoiceFactoryFacade::class,
            'PaymentFacade' => PaymentQueryFacade::class,
            'PurchaseFacade' => PurchaseFacade::class
        ];
    }
}
