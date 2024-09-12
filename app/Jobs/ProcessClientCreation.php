<?php

namespace App\Jobs;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ClientPdfService;
use App\Services\EmailService;

class ProcessClientCreation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function handle(ClientPdfService $pdfService, EmailService $emailService)
    {
        // Génération du PDF
        $pdfPath = $pdfService->generateClientPdf($this->client);

        // Envoi de l'email
        $emailService->sendClientCreationEmail($this->client->email, $this->client, $pdfPath);
    }
}