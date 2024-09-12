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

    public function addPaiement(Request $request, Dette $dette)
    {
        $data = $request->validate([
            'montant' => 'required|numeric|min:0',
        ]);

        $dette = $this->detteService->effectuerPaiement($dette, $data['montant']);

        return response()->json($dette);
    }

    // public function show(Dette $dette)
    // {
    //     return response()->json($dette->load('paiements'));
    // }

    public function show(Dette $dette)
    {
        $dette->load('paiements');
        return response()->json([
            'dette' => $dette,
            'montantRestant' => $dette->montantRestant,
            'estSoldee' => $dette->montantRestant <= 0,
        ]);
    }
    public function clientDettes(Client $client)
    {
        return response()->json($client->dettes()->with('paiements')->get());
    }
}
