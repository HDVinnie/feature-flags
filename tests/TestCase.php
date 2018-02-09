<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return ['Madewithlove\FeatureFlags\FeatureFlagsServiceProvider'];
    }

    protected function getPackageAliases($app)
    {
        return [
            'FeatureFlag' => 'Madewithlove\FeatureFlags\Facades\FeatureFlag',
        ];
    }
}
