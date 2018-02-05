<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Feature Flags Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection where Feature Flags are
    | going to be cached.
    |
    */
    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Feature Flags Redis Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used when storing all Feature Flags data in Redis.
    |
    */
    'prefix' => env('FEATURE_FLAGS_PREFIX', 'feature-flags:'),
];