<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Facades\UploadFacade as Upload;

class UploadClientImageListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ClientCreated $event)
    {
        $client = $event->client;
        if ($client->user && $client->user->photo && !strpos($client->user->photo, 'cloudinary')) {
            // Si la photo n'est pas sur Cloudinary, essayons de l'uploader
            $newPhotoUrl = Upload::uploadPhoto($client->user->photo, 'clients');
            if (strpos($newPhotoUrl, 'cloudinary') !== false) {
                // Si l'upload sur Cloudinary a réussi, mettons à jour l'URL dans la base de données
                $client->user->update(['photo' => $newPhotoUrl]);
                // Supprimons l'ancienne photo locale
                Upload::deletePhoto($client->user->photo);
            }
        }
    }
}