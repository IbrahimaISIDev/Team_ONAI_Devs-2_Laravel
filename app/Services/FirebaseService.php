<?php

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

class FirebaseService
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $this->database = $factory->createDatabase();
    }

    public function getData($path)
    {
        return $this->database->getReference($path)->getValue();
    }

    public function setData($path, $data)
    {
        $this->database->getReference($path)->set($data);
    }

    public function store(array $data)
    {
        $this->database->getReference('dettes_archivees')->push($data);
    }

    public function retrieve(array $query)
    {
        return $this->database->getReference('dettes_archivees')
            ->orderByChild(key($query))
            ->equalTo(current($query))
            ->getValue();
    }

    public function delete(array $query)
    {
        $ref = $this->database->getReference('dettes_archivees');
        $snapshot = $ref->orderByChild(key($query))->equalTo(current($query))->getSnapshot();
        foreach ($snapshot->getValue() as $key => $value) {
            $ref->getChild($key)->remove();
        }
    }
}
