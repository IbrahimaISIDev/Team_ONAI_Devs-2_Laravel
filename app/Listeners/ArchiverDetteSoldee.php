<?php
namespace App\Listeners;

use App\Events\DetteSoldee;
use App\Services\ArchiveService;

class ArchiverDetteSoldee
{
    protected $archiveService;

    public function __construct(ArchiveService $archiveService)
    {
        $this->archiveService = $archiveService;
    }

    public function handle(DetteSoldee $event)
    {
        $this->archiveService->archiveDettesPayees($event->dette);
    }
}