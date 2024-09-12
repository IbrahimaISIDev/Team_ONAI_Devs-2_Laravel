<?php

namespace App\Repositories;

use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClientPdfRepository
{
    public function generateQrCodeData(Client $client)
    {
        return json_encode([
            'surname' => $client->surname,
            'telephone' => $client->telephone,
        ]);
    }

    public function generateBase64QrCode($qrCodeData)
    {
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeData);

        return 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);
    }

    public function generateAndSavePdf(Client $client, $base64QrCode)
    {
        Log::info('Generating PDF for client: ' . $client->id);
        
        $photoData = null;
        if ($client->user && $client->user->photo) {
            Log::info('Photo URL: ' . $client->user->photo);
            try {
                $response = Http::get($client->user->photo);
                if ($response->successful()) {
                    $photoData = 'data:' . $response->header('Content-Type') . ';base64,' . base64_encode($response->body());
                    Log::info('Photo successfully retrieved and encoded');
                } else {
                    Log::warning('Failed to retrieve photo: ' . $response->status());
                }
            } catch (\Exception $e) {
                Log::error('Error retrieving photo: ' . $e->getMessage());
            }
        } else {
            Log::info('No photo available for this client');
        }

        $pdf = Pdf::loadView('pdfs.client', [
            'client' => $client,
            'qrCodeSvg' => $base64QrCode,
            'photoData' => $photoData
        ]);

        $filename = 'client_' . $client->id . '.pdf';
        $path = 'pdfs/' . $filename;
        Storage::disk('public')->makeDirectory('pdfs');
        Storage::disk('public')->put($path, $pdf->output());

        Log::info('PDF generated and saved: ' . $path);

        return Storage::disk('public')->path($path);
    }
}