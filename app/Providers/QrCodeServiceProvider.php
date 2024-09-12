<?php

namespace App\Providers;

use App\Services\QrCodeService;
use App\Repositories\QrCodeRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\QrCodeRepositoryInterface;

class QrCodeServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind the QrCodeService to the container
        $this->app->singleton(QrCodeService::class, function ($app) {
            return new QrCodeService();
        });
        $this->app->bind(QrCodeRepositoryInterface::class, QrCodeRepository::class);
    }

    public function boot()
    {
        // Additional boot logic if required
    }
}
