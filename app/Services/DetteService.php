<?php
namespace App\Services;

use App\Models\Dette;
use Illuminate\Support\Facades\DB;
use App\Events\DetteSoldee;

class DetteService
{
    public function creerDette(array $data)
    {
        return DB::transaction(function () use ($data) {
            $dette = Dette::create([
                'montant' => $data['montant'],
                'client_id' => $data['client_id'],
            ]);

            foreach ($data['articles'] as $article) {
                DB::table('details_dettes')->insert([
                    'dette_id' => $dette->id,
                    'article_id' => $article['id'],
                    'prix' => $article['prix'],
                    'quantite' => $article['quantite'],
                ]);
            }

            return $dette;
        });
    }

    public function effectuerPaiement(Dette $dette, float $montant)
    {
        return DB::transaction(function () use ($dette, $montant) {
            $dette->paiements()->create(['montant' => $montant]);
            if ($dette->montantRestant <= 0) {
                event(new DetteSoldee($dette));
            }
            return $dette->fresh();
        });
    }
}
