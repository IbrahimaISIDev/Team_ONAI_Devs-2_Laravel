<?php

namespace App\Repositories;

use App\Repositories\Interfaces\MessageRepositoryInterface;
use Twilio\Rest\Client;

class TwilioMessageRepository implements MessageRepositoryInterface
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
    }

    public function sendMessage($to, $message)
    {
        return $this->twilio->messages->create($to, [
            'from' => config('services.twilio.from'),
            'body' => $message
        ]);
    }   
}
