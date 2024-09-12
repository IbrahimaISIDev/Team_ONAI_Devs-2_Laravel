<?php

namespace App\Services;

use App\Models\Client;
use App\Repositories\ClientPdfRepository;

class ClientPdfService
{
    protected $clientPdfRepository;

    public function __construct(ClientPdfRepository $clientPdfRepository)
    {
        $this->clientPdfRepository = $clientPdfRepository;
    }

    public function generateClientPdf(Client $client)
    {
        $qrCodeData = $this->clientPdfRepository->generateQrCodeData($client);
        $base64QrCode = $this->clientPdfRepository->generateBase64QrCode($qrCodeData);
        
        $pdfPath = $this->clientPdfRepository->generateAndSavePdf($client, $base64QrCode);
        
        return $pdfPath;
    }
}