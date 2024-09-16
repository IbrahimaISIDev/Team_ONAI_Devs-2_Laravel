<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ArchiveRepositoryInterface;

class ArchiveRepository implements ArchiveRepositoryInterface
{
    protected $archiveRepo;

    public function __construct(ArchiveRepositoryInterface $archiveRepo)
    {
        $this->archiveRepo = $archiveRepo;
    }

    public function archiver(array $data)
    {
        $this->archiveRepo->archiver($data);
    }

    // Implement retrieve and restore methods if needed
    public function retrieve(array $data)
    {
        return $this->archiveRepo->retrieve($data);
    }

    public function restore(array $data)
    {
        return $this->archiveRepo->restore($data);
    }
}
