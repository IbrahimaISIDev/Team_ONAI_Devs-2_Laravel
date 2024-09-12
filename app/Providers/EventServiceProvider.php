<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\ClientCreated;
use App\Listeners\UploadClientImageListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ClientCreated::class => [
            UploadClientImageListener::class,
        ],
        ClientCreated::class => [
            \App\Listeners\ProcessClientCreationListener::class,
        ],
        'App\Events\DetteSoldee' => [
            'App\Listeners\ArchiverDetteSoldee',
        ],

    ];

    public function boot()
    {
        //
    }
}
