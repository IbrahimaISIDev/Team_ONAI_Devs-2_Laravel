<?php

namespace App\Console\Commands;

use App\Jobs\RetryFailedUploadsJob;
use Illuminate\Console\Command;

class RetryUploadsCommand extends Command
{
    protected $signature = 'uploads:retry';
    protected $description = 'Retry failed client image uploads to Cloudinary';

    public function handle()
    {
        $this->info('Dispatching job to retry failed uploads...');
        RetryFailedUploadsJob::dispatch();
        $this->info('Job dispatched successfully.');
    }
}