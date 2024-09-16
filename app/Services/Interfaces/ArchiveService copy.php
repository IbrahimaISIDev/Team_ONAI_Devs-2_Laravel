<?php

namespace App\Services;

use App\Models\Dette;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interfaces\ArchiveRepositoryInterface;
use App\Interfaces\CloudStorageInterface;

class ArchiveService
{
    protected $repository;
    protected $cloudStorage;

    public function __construct(ArchiveRepositoryInterface $repository, CloudStorageInterface $cloudStorage)
    {
        $this->repository = $repository;
        $this->cloudStorage = $cloudStorage;
    }

    // Méthode pour centraliser la récupération des données du cloud
    protected function retrieveFromCloud(array $query)
    {
        return $this->cloudStorage->retrieve($query);
    }

    // Méthode pour restaurer une dette spécifique
    protected function restoreDette($dette)
    {
        if (!isset($dette['id'])) {
            Log::error("La dette n'a pas d'ID pour être restaurée.");
            return;
        }

        Dette::create($dette);
        $this->cloudStorage->delete(['id' => $dette['id']]);
    }

    // Archiver les dettes payées ou soldées
    public function archiveDettesPayees()
    {
        Log::info('Démarrage de la méthode archiveDettesPayees');

        // Récupérer les dettes soldées
        $dettesSoldées = Dette::with(['client', 'articles', 'paiements'])
            ->whereRaw('montant = (SELECT SUM(montant) FROM paiements WHERE dette_id = dettes.id)')
            ->get();

        Log::info('Nombre de dettes soldées trouvées : ' . $dettesSoldées->count());

        $archivedCount = 0;
        foreach ($dettesSoldées as $dette) {
            Log::info('Traitement de la dette ID : ' . $dette->id);

            $data = [
                'dette' => $dette->toArray(),
                'archived_at' => now()->toDateTimeString(),
            ];

            Log::info('Données préparées pour l\'archivage : ', $data);

            try {
                // Archiver la dette dans MongoDB
                $archivedId = $this->cloudStorage->store($data);
                Log::info('Dette archivée avec succès : ' . $dette->id . ', ID MongoDB : ' . $archivedId);

                // Supprimer les paiements, articles et la dette elle-même
                $this->deleteDetteRelations($dette);
                $archivedCount++;
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'archivage de la dette ID ' . $dette->id . ' : ' . $e->getMessage());
            }
        }

        Log::info('Fin de la méthode archiveDettesPayees. Nombre total de dettes archivées : ' . $archivedCount);
    }

    // Méthode pour centraliser la suppression des paiements, articles, et dettes
    protected function deleteDetteRelations($dette)
    {
        try {
            $dette->paiements()->delete();
            Log::info('Paiements supprimés pour la dette : ' . $dette->id);

            $dette->articles()->delete();
            Log::info('Articles supprimés pour la dette : ' . $dette->id);

            $dette->delete();
            Log::info('Dette supprimée : ' . $dette->id);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression des relations de la dette ID ' . $dette->id . ' : ' . $e->getMessage());
        }
    }
    // Récupérer les dettes archivées
    public function getArchivedDettes($clientId = null, $date = null)
    {
        $query = [];
        if ($clientId) {
            $query['dette.client_id'] = $clientId;
        }
        if ($date) {
            $query['archived_at'] = ['$gte' => $date];
        }
        Log::info('ArchiveService: Retrieving archived dettes', ['query' => $query]);
        $results = $this->cloudStorage->retrieve($query);
        Log::info('ArchiveService: Retrieved archived dettes', ['count' => count($results)]);
        return $results;
    }

    // Récupérer les détails d'une dette archivée
    public function getArchivedDetteDetails($detteId)
    {
        return $this->retrieveFromCloud(['id' => $detteId]);
    }

    // Restaurer une dette archivée
    public function restoreArchivedDette($detteId)
    {
        $dette = $this->retrieveFromCloud(['id' => $detteId])[0];
        $this->restoreDette($dette);
    }

    // Restaurer les dettes d'un client
    public function restoreClientDettes($clientId)
    {
        $dettes = $this->retrieveFromCloud(['client_id' => $clientId]);
        foreach ($dettes as $dette) {
            $this->restoreDette($dette);
        }
    }

    // Restaurer les dettes archivées par date
    public function restoreArchivedDettesByDate($date)
    {
        $dettes = $this->retrieveFromCloud(['date' => $date]);
        foreach ($dettes as $dette) {
            $this->restoreDette($dette);
        }
    }
}


