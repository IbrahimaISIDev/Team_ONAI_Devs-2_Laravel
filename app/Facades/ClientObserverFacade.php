<?php

namespace App\Facades;

use App\Models\Client;
use App\Observers\ClientObserver;
use Illuminate\Support\Facades\Facade;

class ClientObserverFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'client.observer';
    }

    public static function register()
    {
        Client::observe(ClientObserver::class);
    }
}