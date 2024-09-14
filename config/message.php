<?php

use App\Repositories\TwilioMessageRepository;
use App\Repositories\InfobipMessageRepository;

return [
    'driver' => env('MESSAGE_DRIVER', 'infobip'),

    'drivers' => [
        'twilio' => TwilioMessageRepository::class,
        'infobip' => InfobipMessageRepository::class,
    ],
];
