<?php

namespace App\Http\Controllers;

use App\Models\Dette;
use Illuminate\Http\Request;
use App\Services\ArchiveService;
use App\Jobs\ArchiveDettesPayees;
use Illuminate\Support\Facades\Log;

class ArchiveController extends Controller
{
    protected $archiveService;

    public function __construct(ArchiveService $archiveService)
    {
        $this->archiveService = $archiveService;
    }

    // Archiver les dettes soldées
    public function archiveDettes()
    {
        // Dispatch the job to archive paid debts
        ArchiveDettesPayees::dispatch($this->archiveService);

        return response()->json(['message' => 'Dettes payées seront archivées prochainement.'], 202);
    }

    // Afficher les détails d'une dette spécifique archivée
    public function showArchivedDetails($detteId)
    {
        $dette = $this->archiveService->getArchivedDetteDetails($detteId);
        return response()->json($dette);
    }


    // 1. Afficher les dettes archivées avec filtre
    public function showArchivedDettes(Request $request)
    {
        $clientId = $request->input('client_id');
        $date = $request->input('date');
        $dettes = $this->archiveService->getArchivedDettes($clientId, $date);

        if (empty($dettes)) {
            Log::info('No archived dettes found', ['client_id' => $clientId, 'date' => $date]);
            return response()->json(['message' => 'No archived dettes found'], 404);
        }

        return response()->json($dettes);
    }

    // 2. Afficher les dettes archivées d'un client
    public function showClientArchivedDettes($clientId)
    {
        $dettes = $this->archiveService->getArchivedDetteDetails($clientId);
        return response()->json($dettes);
    }

    // 3. Afficher une dette archivée avec ses détails
    public function showArchivedDetteDetails($detteId)
    {
        $dette = $this->archiveService->getArchivedDetteDetails($detteId);
        return response()->json($dette);
    }

    // 4. Restaurer les dettes archivées à une date donnée
    public function restoreArchivedDettesByDate($date)
    {
        $this->archiveService->restoreArchivedDettesByDate($date);
        return response()->json(['message' => 'Dettes restaurées avec succès']);
    }

    // 5. Restaurer une dette spécifique
    public function restoreDette($detteId)
    {
        $this->archiveService->restoreArchivedDette($detteId);
        return response()->json(['message' => 'Dette restaurée avec succès']);
    }

    // 6. Restaurer les dettes d'un client
    public function restoreClientDettes($clientId)
    {
        $this->archiveService->restoreClientDettes($clientId);
        return response()->json(['message' => 'Dettes du client restaurées avec succès']);
    }
}
