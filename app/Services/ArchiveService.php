<?php

namespace App\Services;

use App\Models\Dette;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interfaces\ArchiveRepositoryInterface; // Correction de l'interface

class ArchiveService
{
    protected $repository;

    public function __construct(ArchiveRepositoryInterface $repository) // Correction ici
    {
        $this->repository = $repository;
    }

    public function archiveDettesPayees()
    {
        Log::info('Starting archiveDettesPayees');

        $dettesPayees = Dette::with(['client', 'articles', 'paiements'])
            ->whereRaw('montant = (SELECT SUM(montant) FROM paiements WHERE dette_id = dettes.id)')
            ->get();

        Log::info('Dettes payees found: ' . $dettesPayees->count());

        foreach ($dettesPayees as $dette) {
            Log::info('Processing dette: ' . $dette->id);

            $data = [
                'dette' => $dette->toArray(),
                // 'client' => $dette->client->toArray(),
                // 'articles' => $dette->articles->toArray(),
                // 'paiements' => $dette->paiements->toArray(),
            ];

            Log::info('Data prepared for archiving: ', $data);

            try {
                $this->repository->archiver($data);
                Log::info('Dette archived successfully: ' . $dette->id);

                $dette->paiements()->delete();
                Log::info('Paiements deleted for dette: ' . $dette->id);

                // Si la relation entre Dette et Article est HasMany
                $dette->articles()->delete(); // Supprime les articles associés
                // OU
                // $dette->articles()->update(['dette_id' => null]); // Dissocie les articles sans les supprimer
                Log::info('Articles handled for dette: ' . $dette->id);

                $dette->delete();
                Log::info('Dette deleted: ' . $dette->id);
            } catch (\Exception $e) {
                Log::error('Error archiving dette ' . $dette->id . ': ' . $e->getMessage());
            }
        }

        Log::info('Finished archiveDettesPayees');
    }
}
