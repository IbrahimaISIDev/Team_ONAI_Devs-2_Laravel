<?php

namespace App\Jobs;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Facades\UploadFacade as Upload;

class UploadClientPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function handle()
    {
        if ($this->client->user && $this->client->user->photo) {
            $localPath = $this->client->user->photo;
            $cloudUrl = Upload::uploadPhoto($localPath, 'clients');
            
            if ($cloudUrl) {
                $this->client->user->update(['photo' => $cloudUrl]);
            }
        }
    }
}