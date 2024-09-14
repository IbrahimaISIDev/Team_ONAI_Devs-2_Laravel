<?php

namespace App\Providers;

use App\Services\CloudStorageService;
use Illuminate\Support\ServiceProvider;
use App\Services\CloudStorage\CloudStorageFactory;
use App\Services\CloudStorage\CloudStorageInterface;

class CloudStorageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CloudStorageInterface::class, function ($app) {
            $config = config('cloud_storage');
            return CloudStorageFactory::create($config['driver']);

            $this->app->bind(
                CloudStorageInterface::class,
                CloudStorageService::class
            );
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/cloud_storage.php' => config_path('cloud_storage.php'),
        ], 'config');
    }
}