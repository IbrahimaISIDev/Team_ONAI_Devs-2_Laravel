<?php

use App\Repositories\MongoDBArchiveRepository;
use App\Repositories\FirebaseArchiveRepository;

return [
    'driver' => env('ARCHIVE_DRIVER', 'mongodb'),

    'drivers' => [
        'mongodb' => MongoDBArchiveRepository::class,
        'firebase' => FirebaseArchiveRepository::class,
    ],
];

