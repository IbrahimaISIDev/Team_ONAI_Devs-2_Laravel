<?php

namespace App\Interfaces;

interface CloudStorageInterface
{
    public function store(array $data);
    public function retrieve(array $query = [], $page = 1, $perPage = 15);
    public function delete(array $query);
    public function update(array $query, array $data);
    public function findById($id);
}