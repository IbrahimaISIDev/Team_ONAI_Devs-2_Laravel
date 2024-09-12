<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dette;
use App\Services\DetteService;

class PaiementController extends Controller
{
    protected $detteService;

    public function __construct(DetteService $detteService)
    {
        $this->detteService = $detteService;
    }

    /**
     * Store a newly created paiement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dette  $dette
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Dette $dette)
    {
        $validatedData = $request->validate([
            'montant' => 'required|numeric|min:0|max:' . $dette->montantRestant,
        ]);

        $dette = $this->detteService->effectuerPaiement($dette, $validatedData['montant']);

        return response()->json($dette, 200);
    }
}
