<?php

namespace App\Interfaces;

interface CloudStorageInterface
{
    public function store(array $data);
    public function retrieve(array $query);
    public function delete(array $query);
}