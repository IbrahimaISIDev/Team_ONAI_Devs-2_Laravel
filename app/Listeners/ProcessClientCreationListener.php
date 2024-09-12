<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use App\Jobs\ProcessClientCreation;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessClientCreationListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  ClientCreated  $event
     * @return void
     */
    public function handle(ClientCreated $event)
    {
        ProcessClientCreation::dispatch($event->client)->onQueue('default');
    }
}