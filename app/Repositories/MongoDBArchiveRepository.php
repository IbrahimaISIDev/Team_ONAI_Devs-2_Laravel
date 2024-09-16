<?php

namespace App\Repositories;

use MongoDB\Client;
use MongoDB\Exception\Exception as MongoDBException;
use App\Repositories\Interfaces\ArchiveRepositoryInterface;
use Illuminate\Support\Facades\Log;

class MongoDBArchiveRepository implements ArchiveRepositoryInterface
{
    protected $client;
    protected $database;

    public function __construct()
    {
        $this->client = new Client(env('MONGODB_URI'));
        $this->database = $this->client->selectDatabase(env('MONGODB_DATABASE'));
    }

    public function archiver(array $data)
    {
        $collection = $this->database->selectCollection('Gestion-Shop');
        try {
            $collection->insertOne($data);
            Log::info('Data archived to MongoDB: ', $data);
        } catch (\Exception $e) {
            Log::error('Error archiving data: ' . $e->getMessage());
        }
    }

    public function retrieve(array $data = [])
    {
        $collection = $this->database->selectCollection('Gestion-Shop');
        try {
            // Convert the MongoDB Cursor to an array
            $result = iterator_to_array($collection->find());
            Log::info('Data retrieved from MongoDB.');
            return $result;
        } catch (\Exception $e) {
            Log::error('Error retrieving data: ' . $e->getMessage());
            return null;
        }
    }

    public function restore(array $data)
    {
        $collection = $this->database->selectCollection('restores/' . date('Y-m-d'));
        try {
            $collection->insertOne($data);
            Log::info('Data restored in MongoDB: ', $data);
        } catch (\Exception $e) {
            Log::error('Error restoring data: ' . $e->getMessage());
        }
    }
}
