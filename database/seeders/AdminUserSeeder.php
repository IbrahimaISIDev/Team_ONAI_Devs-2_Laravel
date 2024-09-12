<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur avec le rôle administrateur
        User::factory()->admin()->create([
            'nom' => 'Admin',
            'prenom' => 'User',
            'login' => 'adminuser',
            'password' => Hash::make('passer123@'), // Assurez-vous d'utiliser un mot de passe sécurisé
            'etat' => 'ACTIF',  // Optionnel, selon vos règles de validation
        ]);
    }
}