<?php

namespace App\Jobs;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Facades\UploadFacade as Upload;

class RetryFailedUploadsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $clientsWithoutCloudinaryImage = Client::whereHas('user', function ($query) {
            $query->whereNotNull('photo')
                  ->where('photo', 'not like', '%cloudinary%');
        })->get();

        foreach ($clientsWithoutCloudinaryImage as $client) {
            $newPhotoUrl = Upload::uploadPhoto($client->user->photo, 'clients');
            if (strpos($newPhotoUrl, 'cloudinary') !== false) {
                $client->user->update(['photo' => $newPhotoUrl]);
                Upload::deletePhoto($client->user->photo);
            }
        }
    }
}