<?php

namespace Madewithlove\FeatureFlags;

use Madewithlove\FeatureFlags\Events;
use Madewithlove\FeatureFlags\Listeners;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class FeatureFlagsServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (! defined('FEATURE_FLAGS_PATH')) {
            define('FEATURE_FLAGS_PATH', realpath(__DIR__.'/../'));
        }

        $this->configure();
        $this->registerFlagsManager();
        $this->offerPublishing();
    }

    private function configure()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/feature-flags.php', 'feature-flags'
        );

        FeatureFlags::use(config('feature-flags.use'));
    }

    private function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/feature-flags.php' => config_path('feature-flags.php'),
            ], 'feature-flags-config');
        }
    }

    private function registerFlagsManager()
    {
        $this->app->alias(FeatureFlagsManager::class, 'feature-flags');
    }

    public function boot()
    {
        $this->registerEventListeners();
        $this->registerViews();
        $this->defineAssetRoot();
        $this->registerMigrations();
    }

    private function registerEventListeners()
    {
        $events = [
            Events\FlagWasCreated::class => Listeners\StoreFlagOnRedisWhenCreated::class,
            Events\FlagWasDisabled::class => Listeners\DisableFlagOnRedis::class,
            Events\FlagWasReEnabled::class => Listeners\ReEnableFlagOnRedis::class,
            Events\FlagByPassRulesWereUpdated::class => Listeners\UpdateFlagByPassRules::class,
        ];

        foreach ($events as $event => $listener) {
            Event::listen($event, $listener);
        }
    }

    private function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'feature-flags');
    }

    private function defineAssetRoot()
    {
        $this->publishes([
            FEATURE_FLAGS_PATH.'/public' => public_path('vendor/feature-flags'),
        ], 'feature-flags-assets');
    }

    private function registerMigrations()
    {
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'feature-flags-migrations');
    }
}