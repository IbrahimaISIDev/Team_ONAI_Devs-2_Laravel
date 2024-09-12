<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\UserService;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;

class UserServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        $this->app->bind('user.service', function ($app) {
            return $app->make(UserServiceInterface::class);
        });

        $this->app->bind('user.repository', function ($app) {
            return $app->make(UserRepositoryInterface::class);
        });
    }

    public function boot()
    {
        //
    }
}