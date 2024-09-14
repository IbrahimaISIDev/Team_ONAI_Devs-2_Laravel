<?php

namespace App\Repositories;

use App\Repositories\Interfaces\MessageRepositoryInterface;
use Illuminate\Support\Facades\Http;

class InfobipMessageRepository implements MessageRepositoryInterface
{
    protected $baseUrl;
    protected $apiKey;
    protected $sender;

    public function __construct()
    {
        $this->baseUrl = config('services.infobip.base_url');
        $this->apiKey = config('services.infobip.api_key');
        $this->sender = config('services.infobip.sender');
    }

    public function sendMessage($to, $message)
    {
        $response = Http::withHeaders([
            'Authorization' => 'App ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post("{$this->baseUrl}/sms/2/text/advanced", [
            'messages' => [
                [
                    'from' => $this->sender,
                    'destinations' => [
                        ['to' => $to]
                    ],
                    'text' => $message
                ]
            ]
        ]);

        return $response->successful();
    }
}
