<?php


namespace Madewithlove\FeatureFlags\Facades;


use Illuminate\Support\Facades\Facade;

class FeatureFlags extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'feature-flags';
    }
}