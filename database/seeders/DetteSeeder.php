<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dette;

class DetteSeeder extends Seeder
{
    public function run()
    {
        // Générer des dettes pour les clients 1 et 2
        $clientsIds = [1, 2];

        foreach ($clientsIds as $clientId) {
            // Créer plusieurs dettes pour chaque client
            Dette::factory()->count(3)->create([
                'client_id' => $clientId,
            ]);
        }
    }
}
