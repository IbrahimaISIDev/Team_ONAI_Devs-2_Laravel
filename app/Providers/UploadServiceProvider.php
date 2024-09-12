<?php

// app/Providers/UploadServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UploadService;
use App\Services\CloudStorage\CloudStorageInterface;
use App\Services\CloudStorage\LocalStorage;

class UploadServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(UploadService::class, function ($app) {
            $primaryStorage = $app->make(CloudStorageInterface::class);
            $fallbackStorage = $app->make(LocalStorage::class);
            return new UploadService($primaryStorage, $fallbackStorage);
        });

        // Enregistrer également comme un alias 'upload.service'
        $this->app->alias(UploadService::class, 'upload.service');
    }
}