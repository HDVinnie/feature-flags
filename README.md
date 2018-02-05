# Feature Flags

Bring feature flags to your Laravel application. Deploy often, release when ready.

## Getting Started

1. Run `composer require madewithlove/feature-flags` (Not published yet, use the [Local Repositories](https://stackoverflow.com/questions/17426192/composer-using-a-local-repository#17428021) composer feature)
2. Publish the configs `php artisan vendor:publish --provider="Madewithlove\\FeatureFlags\\FeatureFlagsServiceProvider"`
3. Run the migrations: `php artisan migrate`
4. Register the dashboard routes in your `AppServiceProvider`, like so:

```php
<?php

use Madewithlove\FeatureFlags\FeatureFlags;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        FeatureFlags::routes();
    }
}
```


Done! Now you can use the Facade `FeatureFlag::isEnabled('FEATURE_NAME')` to check if the feature is enabled for the current user, or `FeatureFlag::isEnabledFor('FEATURE_NAME', $user)`, passing the user.

### Restrict access to the dashboard

At this point, any user can access your dashboard, which is not cool. You can restrict access to the dashboard to only users you want. To do so, you can add this to your `AppServiceProvider`:

```php
<?php

use Madewithlove\FeatureFlags\FeatureFlags;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        FeatureFlags::routes();
        
        FeatureFlags::authUsing(function ($request) {
            $user = $request->user();
            
            return $user && $user->is_admin;
        });
    }
}
```

The `FeatureFlags::authUsing()` method receives a closure and in the closure, you are going to receive the Laravel Request object accessing the dashboard. To deny access you only have to return false from your closure.

## Screenshots

![Dashboard](/docs/dashboard.png)
![New Flag](/docs/dashboard-new-feature-flag.png)
![Bypass Rules](/docs/dashboard-new-feature-flag-bypass.png)
