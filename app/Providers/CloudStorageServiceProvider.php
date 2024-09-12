<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CloudStorage\CloudStorageInterface;
use App\Services\CloudStorage\CloudStorageFactory;

class CloudStorageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CloudStorageInterface::class, function ($app) {
            $config = config('cloud_storage');
            return CloudStorageFactory::create($config['driver']);
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/cloud_storage.php' => config_path('cloud_storage.php'),
        ], 'config');
    }
}