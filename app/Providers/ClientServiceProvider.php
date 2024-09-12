<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Interfaces\ClientServiceInterface;
use App\Services\ClientService;
use App\Repositories\Interfaces\ClientRepositoryInterface;
use App\Repositories\ClientRepository;

class ClientServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ClientServiceInterface::class, ClientService::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);

        $this->app->bind('client.service', function ($app) {
            return $app->make(ClientServiceInterface::class);
        });

        $this->app->bind('client.repository', function ($app) {
            return $app->make(ClientRepositoryInterface::class);
        });
    }

    public function boot()
    {
        //
    }
}