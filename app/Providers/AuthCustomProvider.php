<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\AuthentificationServiceInterface;

class AuthCustomProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AuthentificationServiceInterface::class, function ($app) {
            $authProvider = config('yaml.authentication.provider');
            return app($authProvider);
        });
    }

    public function boot()
    {
        //
    }
}