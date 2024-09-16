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

    public function retrieve(array $data = [])
    {
        Log::info('Retrieving data from Firebase.');
        try {
            $reference = $this->database->getReference('archives');
            $snapshot = $reference->getSnapshot();
            return $snapshot->getValue();
        } catch (\Exception $e) {
            Log::error('Firebase retrieval error: ' . $e->getMessage());
            return null;
        }
    }

    public function restore(array $data)
    {
        Log::info('Restoring data in Firebase: ', $data);
        try {
            // Example restore logic: Re-add data to a different path or restore to original location
            $this->database->getReference('restores/' . date('Y-m-d'))->push($data);
        } catch (\Exception $e) {
            Log::error('Firebase restore error: ' . $e->getMessage());
        }
    }
}
