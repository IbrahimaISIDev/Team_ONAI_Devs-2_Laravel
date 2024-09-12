<?php

namespace App\Observers;

use App\Models\Client;
use App\Events\ClientCreated;
use Illuminate\Contracts\Events\Dispatcher;

class ClientObserver
{
    protected $eventDispatcher;

    public function __construct(Dispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function created(Client $client)
    {
        $this->eventDispatcher->dispatch(new ClientCreated($client));
    }

    // Autres méthodes de l'observer si nécessaire
    public function updated(Client $client) {}
    public function deleted(Client $client) {}
}