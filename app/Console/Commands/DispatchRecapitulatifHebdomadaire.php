<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\EnvoyerRecapitulatifHebdomadaire;
use App\Services\MessageService;

class DispatchRecapitulatifHebdomadaire extends Command
{
    protected $signature = 'dispatch:recap-hebdo';
    protected $description = 'Dispatch the EnvoyerRecapitulatifHebdomadaire job';

    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        parent::__construct();
        $this->messageService = $messageService;
    }

    public function handle()
    {
        EnvoyerRecapitulatifHebdomadaire::dispatch($this->messageService);
        $this->info('Job dispatched successfully!');
    }
}
