<?php

namespace App\Http\Controllers;

use App\Models\Dette;
use App\Models\Client;
use App\Services\DetteService;
use Illuminate\Http\Request;

class DetteController extends Controller
{
    protected $detteService;

    public function __construct(DetteService $detteService)
    {
        $this->detteService = $detteService;
    }

    // Créer une nouvelle dette
    public function store(Request $request)
    {
        $data = $request->validate([
            'montant' => 'required|numeric|min:0',
            'client_id' => 'required|exists:clients,id',
            'articles' => 'required|array',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.prix' => 'required|numeric|min:0',
            'articles.*.quantite' => 'required|integer|min:1',
        ]);

        $dette = $this->detteService->creerDette($data);

        return response()->json($dette, 201);
    }

    // Ajouter un paiement à une dette existante
    public function addPaiement(Request $request, Dette $dette)
    {
        $data = $request->validate([
            'montant' => 'required|numeric|min:0',
        ]);

        $dette = $this->detteService->effectuerPaiement($dette, $data['montant']);

        return response()->json($dette);
    }

    // Afficher les détails d'une dette spécifique
    public function show(Dette $dette)
    {
        $dette->load('paiements');
        return response()->json([
            'dette' => $dette,
            'montantRestant' => $dette->montantRestant,
            'estSoldee' => $dette->montantRestant <= 0,
        ]);
    }

    // Afficher les dettes d'un client spécifique
    public function clientDettes(Client $client)
    {
        return response()->json($client->dettes()->with('paiements')->get());
    }

    // Archiver les dettes soldées
    public function archive()
    {
        $this->detteService->archiveSoldDettes();
        return response()->json(['message' => 'Dettes archivées avec succès']);
    }

    // Afficher toutes les dettes archivées
    public function showArchived()
    {
        $dettes = $this->detteService->getArchivedDettes();
        return response()->json($dettes);
    }

    // Afficher les dettes archivées d'un client spécifique
    public function showClientArchived($clientId)
    {
        $dettes = $this->detteService->getClientArchivedDettes($clientId);
        return response()->json($dettes);
    }

    // Restaurer une dette archivée
    public function restoreDette($detteId)
    {
        $this->detteService->restoreArchivedDette($detteId);
        return response()->json(['message' => 'Dette restaurée avec succès']);
    }

    // Envoyer un rapport hebdomadaire des dettes
    public function sendWeeklyReport($clientId)
    {
        $this->detteService->sendWeeklyReport($clientId);
        return response()->json(['message' => 'Rapport envoyé avec succès']);
    }
}
