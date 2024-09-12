<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ArchiveService;
use App\Jobs\ArchiveDettesPayees;

class ArchiveController extends Controller
{
    protected $archiveService;

    public function __construct(ArchiveService $archiveService)
    {
        $this->archiveService = $archiveService;
    }

    /**
     * Archive all paid debts.
     *
     * @return \Illuminate\Http\Response
     */
    public function archiveDettes()
    {
        // Dispatch the job to archive paid debts
        ArchiveDettesPayees::dispatch($this->archiveService);

        return response()->json(['message' => 'Dettes payées seront archivées prochainement.'], 202);
    }
}
