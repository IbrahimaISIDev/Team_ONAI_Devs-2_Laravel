<?php

namespace App\Models;

use Kreait\Firebase\Contract\Database;

class FirebaseDette
{
    protected $database;
    protected $tableName = 'dettes';

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function create(array $data)
    {
        return $this->database->getReference($this->tableName)->push($data)->getKey();
    }

    public function find($id)
    {
        return $this->database->getReference($this->tableName)->getChild($id)->getValue();
    }

    public function update($id, array $data)
    {
        return $this->database->getReference($this->tableName)->getChild($id)->update($data);
    }

    public function delete($id)
    {
        return $this->database->getReference($this->tableName)->getChild($id)->remove();
    }

    public function all()
    {
        return $this->database->getReference($this->tableName)->getValue();
    }

    public function where($field, $operator, $value)
    {
        return $this->database->getReference($this->tableName)
            ->orderByChild($field)
            ->startAt($value)
            ->endAt($value)
            ->getValue();
    }
}