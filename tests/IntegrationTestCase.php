<?php

namespace Tests;

use Illuminate\Support\Facades\Redis;
use Madewithlove\FeatureFlags\FeatureFlags;

class IntegrationTestCase extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadRoutes();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->withFactories(__DIR__.'/../database/factories');
        Redis::flushall();
    }

    public function tearDown()
    {
        Redis::flushall();

        parent::tearDown();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'featureflags');
        $app['config']->set('database.connections.featureflags', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function loadRoutes()
    {
        FeatureFlags::routes();
    }
}