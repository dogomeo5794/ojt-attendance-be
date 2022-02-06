<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
        // Validator::extend('required_if_old_company', function ($key, $value, $parameters, $validator) {
        //     $request = request();
        //     // if ($request->has('is_new_company') && $request->input('is_new_company') === true) {
        //     //     return "The $key field is required.";
        //     // }
        // });
    }
}
