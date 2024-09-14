<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\EnvoyerRecapitulatifHebdomadaire;

class DispatchRecapitulatifHebdomadaire extends Command
{
    protected $signature = 'dispatch:recap-hebdo';
    protected $description = 'Envoie le récapitulatif hebdomadaire des dettes aux clients';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        EnvoyerRecapitulatifHebdomadaire::dispatch();
        $this->info('Le récapitulatif hebdomadaire a été envoyé.');
    }
}
