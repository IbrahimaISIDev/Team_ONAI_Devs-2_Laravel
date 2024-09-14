<?php

namespace App\Jobs;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use App\Services\FactureService;
use App\Services\MessageService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EnvoyerRecapitulatifHebdomadaire implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        // Aucune dépendance injectée ici
    }

    public function handle()
    {
        $factureService = app(FactureService::class); // Utilisation du conteneur Laravel pour obtenir l'instance
        $clients = Client::with('dettes')->get();

        foreach ($clients as $client) {
            if ($client instanceof Client) {
                $pdfFile = $factureService->generateRecapitulatif($client);
                // Faites quelque chose avec $pdfFile
            } else {
                Log::error('Expected instance of App\Models\Client, got: ' . get_class($client));
            }
        }

        $messageService = app(MessageService::class);
        $messageService->envoyerRecapitulatifHebdomadaire();
    }
}
