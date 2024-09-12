<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Dette;
use App\Models\Client;

class DetteFactory extends Factory
{
    protected $model = Dette::class;

    public function definition()
    {
        // Récupérer un client aléatoire
        $client = Client::inRandomOrder()->first();

        return [
            'client_id' => $client->id,
            'date' => $this->faker->date(),
            'montant' => $this->faker->randomFloat(2, 100, 1000),
            'montantDu' => $this->faker->randomFloat(2, 0, 500),
            'montantRestant' => $this->faker->randomFloat(2, 0, 500),
        ];
    }
}
