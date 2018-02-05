<?php


namespace Madewithlove\FeatureFlags\Facades;


use Illuminate\Support\Facades\Facade;

class FeatureFlag extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'feature-flags';
    }
}