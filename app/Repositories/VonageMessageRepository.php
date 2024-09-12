<?php

namespace App\Repositories;

use App\Repositories\Interfaces\MessageRepositoryInterface;
use Vonage\Client\Credentials\Basic;
use Vonage\Client as VonageClient;

class VonageMessageRepository implements MessageRepositoryInterface
{
    protected $vonageClient;

    public function __construct()
    {
        $basic  = new Basic(config('services.vonage.api_key'), config('services.vonage.api_secret'));
        $this->vonageClient = new VonageClient($basic);
    }

    public function sendMessage($to, $message)
    {
        $response = $this->vonageClient->sms()->send(
            new \Vonage\SMS\Message\SMS($to, config('services.vonage.sender_id'), $message)
        );

        $messageResponse = $response->current();

        if ($messageResponse->getStatus() == 0) {
            return "Message sent successfully!";
        } else {
            throw new \Exception("Message failed with status: " . $messageResponse->getStatus());
        }
    }
}
