<?php

namespace Madewithlove\FeatureFlags;

use Exception;
use Illuminate\Support\Facades\Route;

class FeatureFlags
{
    public static $auth;

    /**
     * Registers the FeatureFlags package routes.
     */
    public static function routes()
    {
        $options = [
            'prefix' => 'feature-flags',
            'namespace' => 'Madewithlove\FeatureFlags\Http\Controllers',
            'middleware' => config('feature-flags.middleware', 'web'),
        ];

        Route::group($options, function () {
            Route::get('/', 'DashboardController@index');

            Route::resource('flags', 'FeatureFlagsController')->only(['index', 'store', 'update']);
            Route::resource('disabled-flags', 'DisabledFlagsController')->only(['store']);
            Route::resource('enabled-flags', 'EnabledFlagsController')->only(['store']);
        });
    }

    /**
     * Allows users of the package to use any validation they want.
     *
     * @param $callable
     *
     * @return static
     */
    public static function authUsing($callable)
    {
        static::$auth = $callable;
        return new static;
    }

    /**
     * Verifies if the current given request is allowed to proceed.
     *
     * @param $request
     * @return mixed
     */
    public static function check($request)
    {
        return (static::$auth ?: function () {
            return app()->environment('local');
        })($request);
    }

    /**
     * @param string $connectionKey
     *
     * @throws Exception
     */
    public static function use($connectionKey)
    {
        if (is_null($config = config("database.redis.{$connectionKey}"))) {
            throw new Exception("Redis connection [{$connectionKey}] has not been configured.");
        }

        config(['database.redis.feature-flags' => array_merge($config, [
            'options' => [
                'prefix' => config('feature-flags.prefix') ?: 'feature-flags:',
            ],
        ])]);
    }
}