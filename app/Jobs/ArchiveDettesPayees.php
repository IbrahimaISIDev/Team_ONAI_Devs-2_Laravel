<?php

namespace App\Jobs;

use App\Models\Dette;
use Illuminate\Bus\Queueable;
use App\Services\ArchiveService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ArchiveDettesPayees implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(ArchiveService $archiveService)
    {
        Log::info('Handling ArchiveDettesPayees job');

        Dette::whereRaw('montant <= (SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE dette_id = dettes.id)')
            ->get()
            ->each(function ($dette) use ($archiveService) {
                Log::info('Archiving dette: ' . $dette->id);
                $archiveService->archiveDettesPayees($dette);
            });
    }
}
