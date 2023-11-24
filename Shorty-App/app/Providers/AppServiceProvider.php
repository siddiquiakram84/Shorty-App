<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot()
    {
        Validator::extend('has_domain_extension', function ($attribute, $value, $parameters, $validator) {
            $urlParts = parse_url($value);

            // Check if the URL has a host and if it has a valid domain extension
            return isset($urlParts['host']) && preg_match('/\.[a-zA-Z]{2,}\z/', $urlParts['host']);
    });

    Validator::replacer('has_domain_extension', function ($message, $attribute, $rule, $parameters) {
        return false;
    });
    }
}
