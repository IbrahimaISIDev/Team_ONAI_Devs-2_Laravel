<?php

namespace App\Providers;

use App\Models\Client;
use Illuminate\Support\ServiceProvider;

class MongoDBServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('mongodb', function ($app) {
            $uri = env('MONGODB_URI');
            return new Client($uri);
        });
    }
}
