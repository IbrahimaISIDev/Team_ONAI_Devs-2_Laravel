<?php

namespace App\Mail;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class ClientCreationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $pdfPath;

    public function __construct(Client $client, string $pdfPath)
    {
        $clientD = Client::with("user")->find($client->id);
        Log::info($clientD);
        $this->client = new ClientResource($clientD);
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->view('emails.client_creation')
                    ->subject('Bienvenue chez nous!')
                    ->attach($this->pdfPath);
    }
}