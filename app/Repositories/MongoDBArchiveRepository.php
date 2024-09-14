<?php

namespace App\Repositories;

use MongoDB\Client;
use App\Repositories\Interfaces\ArchiveRepositoryInterface;

class MongoDBArchiveRepository implements ArchiveRepositoryInterface
{
    protected $client;
    protected $database;

    public function __construct()
    {
        $this->client = new Client(env('MONGODB_URI'));
        $this->database = $this->client->selectDatabase(env('MONGODB_DATABASE'));
        //dd($this->database);
    }

    public function archiver(array $data)
    {
        $collection = $this->database->selectCollection(date('Y-m-d'));
        $collection->insertOne($data);
    }
}
