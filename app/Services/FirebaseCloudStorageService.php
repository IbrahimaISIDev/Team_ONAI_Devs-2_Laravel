<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use Illuminate\Support\Facades\Log;
use App\Interfaces\CloudStorageInterface;

class FirebaseRealtimeDatabaseService implements CloudStorageInterface
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $this->database = $factory->createDatabase();
    }

    protected function getReference($date = null)
    {
        $datePath = $date ?: date('Y-m-d');
        return $this->database->getReference('archives/' . $datePath);
    }

    public function store(array $data, $date = null)
    {
        Log::info('FirebaseRealtimeDatabaseService: Storing data', ['data' => $data, 'date' => $date]);
        $reference = $this->getReference($date);
        $newReference = $reference->push($data);
        return $newReference->getKey();
    }

    public function retrieve(array $query = [], $page = 1, $perPage = 20, $date = null)
    {
        Log::info('FirebaseRealtimeDatabaseService: Attempting to retrieve data', ['query' => $query, 'date' => $date]);
        $reference = $this->getReference($date);
        $queryRef = $reference;

        foreach ($query as $key => $value) {
            $queryRef = $queryRef->orderByChild($key)->equalTo($value);
        }

        $snapshot = $queryRef->limitToFirst($perPage)->startAt(($page - 1) * $perPage)->getSnapshot();
        $results = [];

        foreach ($snapshot->getValue() as $key => $data) {
            $results[] = $data;
            Log::debug('Retrieved data', ['key' => $key, 'data' => $data]);
        }

        Log::info('FirebaseRealtimeDatabaseService: Retrieved data', ['count' => count($results)]);
        return $results;
    }

    public function delete(array $query, $date = null)
    {
        Log::info('FirebaseRealtimeDatabaseService: Deleting data', ['query' => $query, 'date' => $date]);
        $reference = $this->getReference($date);
        $snapshot = $reference->getSnapshot();
        $deletedCount = 0;

        foreach ($snapshot->getValue() as $key => $data) {
            if ($this->matchesQuery($data, $query)) {
                $reference->getChild($key)->remove();
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    public function update(array $query, array $data, $date = null)
    {
        Log::info('FirebaseRealtimeDatabaseService: Updating data', ['query' => $query, 'data' => $data, 'date' => $date]);
        $reference = $this->getReference($date);
        $snapshot = $reference->getSnapshot();
        $updatedCount = 0;

        foreach ($snapshot->getValue() as $key => $existingData) {
            if ($this->matchesQuery($existingData, $query)) {
                $reference->getChild($key)->update($data);
                $updatedCount++;
            }
        }

        return $updatedCount;
    }

    protected function matchesQuery(array $data, array $query)
    {
        foreach ($query as $key => $value) {
            if (!isset($data[$key]) || $data[$key] !== $value) {
                return false;
            }
        }
        return true;
    }

    public function findById($id, $date = null)
    {
        Log::info('FirebaseRealtimeDatabaseService: Finding by ID', ['id' => $id, 'date' => $date]);
        $reference = $this->getReference($date);
        $snapshot = $reference->getChild($id)->getSnapshot();
        return $snapshot->exists() ? $snapshot->getValue() : null;
    }
}
