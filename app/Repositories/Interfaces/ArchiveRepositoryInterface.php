<?php

namespace App\Repositories\Interfaces;

interface ArchiveRepositoryInterface
{
    public function archiver(array $data);
    public function retrieve(array $data);
    public function restore(array $data);
}
