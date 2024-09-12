<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DetteService;

class DetteServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DetteService::class, function ($app) {
            return new DetteService();
        });
    }
}