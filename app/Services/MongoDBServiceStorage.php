<?php

namespace App\Services;

use MongoDB\Client;
use Illuminate\Support\Facades\Log;
use App\Interfaces\CloudStorageInterface;
use MongoDB\Laravel\Eloquent\Casts\ObjectId;

class MongoDBStorageService implements CloudStorageInterface
{
    protected $client;
    protected $database;

    public function __construct()
    {
        $this->client = new Client(env('MONGODB_URI'));
        $this->database = $this->client->selectDatabase(env('MONGODB_DATABASE'));
    }

    protected function getCollectionName($date = null)
    {
        return $date ?: date('Y-m-d'); // Utilise la date actuelle si aucune date n'est fournie
    }

    protected function getCollection($date = null)
    {
        $collectionName = $this->getCollectionName($date);
        return $this->database->selectCollection($collectionName);
    }

    public function store(array $data, $date = null)
    {
        Log::info('MongoDBStorageService: Storing data', ['data' => $data, 'date' => $date]);
        $collection = $this->getCollection($date);
        $result = $collection->insertOne($data);
        return (string)$result->getInsertedId();
    }

    public function retrieve(array $query = [], $page = 1, $perPage = 20, $date = null)
    {
        Log::info('MongoDBStorageService: Attempting to retrieve data', ['query' => $query, 'date' => $date]);
        try {
            $collection = $this->getCollection($date);
            $options = [
                'skip' => ($page - 1) * $perPage,
                'limit' => $perPage,
            ];
            $cursor = $collection->find($query, $options);
            $results = [];
            foreach ($cursor as $document) {
                $results[] = $document;
                Log::debug('Retrieved document', ['id' => (string)$document['_id']]);
            }
            Log::info('MongoDBStorageService: Retrieved data', ['count' => count($results)]);
            return $results;
        } catch (\Exception $e) {
            Log::error('Error retrieving data from MongoDB', ['error' => $e->getMessage()]);
            return [];
        }
    }

    public function delete(array $query, $date = null)
    {
        Log::info('MongoDBStorageService: Deleting data', ['query' => $query, 'date' => $date]);
        $collection = $this->getCollection($date);
        $result = $collection->deleteMany($query);
        return $result->getDeletedCount();
    }

    public function update(array $query, array $data, $date = null)
    {
        Log::info('MongoDBStorageService: Updating data', ['query' => $query, 'data' => $data, 'date' => $date]);
        $collection = $this->getCollection($date);
        $result = $collection->updateMany($query, ['$set' => $data]);
        return $result->getModifiedCount();
    }

    public function findById($id, $date = null)
    {
        Log::info('MongoDBStorageService: Finding by ID', ['id' => $id, 'date' => $date]);
        $collection = $this->getCollection($date);
        return $collection->findOne(['_id' => new ObjectId($id)]);
    }
}
