<?php

namespace App\Services;

use App\Interfaces\CloudStorageInterface;
use MongoDB\Client;

class MongoDBServiceStorage implements CloudStorageInterface
{
    protected $client;
    protected $database;
    protected $collection;

    public function __construct()
    {
        $this->client = new Client(env('MONGODB_URI'));
        $this->database = $this->client->selectDatabase(env('MONGODB_DATABASE'));
        $this->collection = $this->database->selectCollection('dettes_archivees');
    }

    public function store(array $data)
    {
        $this->collection->insertOne($data);
    }

    public function retrieve(array $query)
    {
        // Utiliser iterator_to_array pour convertir le curseur en tableau
        return iterator_to_array($this->collection->find($query), false);
    }

    public function delete(array $query)
    {
        $this->collection->deleteMany($query);
    }
}