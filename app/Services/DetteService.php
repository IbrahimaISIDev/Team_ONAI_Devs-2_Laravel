<?php

namespace App\Services;

use App\Models\Dette;
use Illuminate\Support\Facades\DB;
use App\Events\DetteSoldee;
use App\Events\DetteArchived;
use App\Interfaces\CloudStorageInterface;
use App\Interfaces\MessageServiceInterface;

class DetteService
{
    protected $cloudStorage;
    protected $messageService;

    public function __construct(CloudStorageInterface $cloudStorage, MessageServiceInterface $messageService)
    {
        $this->cloudStorage = $cloudStorage;
        $this->messageService = $messageService;
    }

    // Créer une dette avec des articles associés
    public function creerDette(array $data)
    {
        return DB::transaction(function () use ($data) {
            $dette = Dette::create([
                'montant' => $data['montant'],
                'client_id' => $data['client_id'],
            ]);

            foreach ($data['articles'] as $article) {
                DB::table('details_dettes')->insert([
                    'dette_id' => $dette->id,
                    'article_id' => $article['id'],
                    'prix' => $article['prix'],
                    'quantite' => $article['quantite'],
                ]);
            }

            return $dette;
        });
    }

    // Effectuer un paiement sur une dette
    public function effectuerPaiement(Dette $dette, float $montant)
    {
        return DB::transaction(function () use ($dette, $montant) {
            $dette->paiements()->create(['montant' => $montant]);

            // Vérifier si la dette est soldée
            if ($dette->montantRestant <= 0) {
                event(new DetteSoldee($dette));
            }

            return $dette->fresh();
        });
    }

    // Archiver les dettes soldées
    public function archiveSoldDettes()
    {
        $soldDettes = Dette::where('solde', true)->get();
        foreach ($soldDettes as $dette) {
            $this->cloudStorage->store($dette->toArray());
            event(new DetteSoldee($dette));
            $dette->delete(); // Supprimer la dette après archivage
        }
    }

    // Récupérer toutes les dettes archivées
    public function getArchivedDettes()
    {
        return $this->cloudStorage->retrieve([]);
    }

    // Récupérer les dettes archivées d'un client
    public function getClientArchivedDettes($clientId)
    {
        return $this->cloudStorage->retrieve(['client_id' => $clientId]);
    }

    // Récupérer les détails d'une dette archivée
    public function getArchivedDetteDetails($detteId)
    {
        return $this->cloudStorage->retrieve(['id' => $detteId]);
    }

    // Restaurer des dettes archivées à partir d'une date donnée
    public function restoreArchivedDettes($date)
    {
        $dettes = $this->cloudStorage->retrieve(['date' => $date]);
        foreach ($dettes as $dette) {
            Dette::create($dette);
            $this->cloudStorage->delete(['id' => $dette['id']]);
        }
    }

    // Restaurer une dette archivée spécifique
    public function restoreArchivedDette($detteId)
    {
        $dette = $this->cloudStorage->retrieve(['id' => $detteId])[0];
        Dette::create($dette);
        $this->cloudStorage->delete(['id' => $detteId]);
    }

    // Restaurer les dettes archivées d'un client
    public function restoreClientArchivedDettes($clientId)
    {
        $dettes = $this->cloudStorage->retrieve(['client_id' => $clientId]);
        foreach ($dettes as $dette) {
            Dette::create($dette);
            $this->cloudStorage->delete(['id' => $dette['id']]);
        }
    }

    // Envoyer un rapport hebdomadaire des dettes d'un client
    public function sendWeeklyReport($clientId)
    {
        $totalDettes = Dette::where('client_id', $clientId)->sum('montant');
        $message = "Votre total de dettes pour cette semaine est de : $totalDettes";
        $this->messageService->sendMessage($clientId, $message);
    }
}
