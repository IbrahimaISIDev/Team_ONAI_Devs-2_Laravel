<?php

namespace App\Repositories;

use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interfaces\ArchiveRepositoryInterface;

class FirebaseArchiveRepository implements ArchiveRepositoryInterface
{
    protected $database;

    public function __construct()
    {
        $path = '/home/dev/Documents/ONAI_Devs/storage/firebase/gestion-boutique-33c69-firebase-adminsdk-wi1ni-ecca6c4bed.json';

        $factory = (new Factory)
            ->withServiceAccount($path)
            ->withDatabaseUri('https://gestion-boutique-33c69-default-rtdb.firebaseio.com');

        $this->database = $factory->createDatabase();
    }

    public function archiver(array $data)
    {
        Log::info('Archiving data to Firebase: ', $data);
        try {
            $this->database->getReference('archives/' . date('Y-m-d'))->push($data);
        } catch (\Exception $e) {
            Log::error('Firebase archiving error: ' . $e->getMessage());
        }
    }
}
