<?php

namespace App\Events;

use App\Models\Client;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ClientCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $client;

    /**
     * Create a new event instance.
     *
     * @param  Client  $client
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}