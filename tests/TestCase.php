<?php

namespace Tests;

use Daniesy\Rodels\RodelsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations before each test
        $this->artisan('migrate', ['--database' => 'testing'])->run();
    }

    protected function getPackageProviders($app): array
    {
        return [
            RodelsServiceProvider::class,
        ];
    }
}
