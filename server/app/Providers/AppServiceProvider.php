<?php

namespace App\Providers;

use App\Utils\ValidationUtils;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('alpha_num_space', function ($attribute, $value) {
            return ValidationUtils::isAlphaNumericSpaceOnly($value);
        });

        Validator::extend('plan_enum', function ($attribute, $value) {
            return ValidationUtils::isPlanEnum($value);
        });
    }
}
