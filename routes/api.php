<?php

use Illuminate\Http\Request;
use App\Jobs\ArchiveDettesPayees;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DetteController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PaiementController;
use App\Jobs\EnvoyerRecapitulatifHebdomadaire;

Route::prefix('v1')->group(function () {
    /**
     * Authentification (Accès sans authentification requise)
     */
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    /**
     * Routes protégées (auth:api, blacklisted)
     */
    Route::middleware(['auth:api', 'blacklisted'])->group(function () {

        /**
         * Authentification et utilisateur
         */
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::apiResource('/users', UserController::class);

        /**
         * Gestion des clients
         */
        Route::prefix('clients')->group(function () {
            Route::post('/telephone', [ClientController::class, 'getByPhoneNumber']);
            Route::post('/register', [ClientController::class, 'addAccount']);
            Route::get('/{id}/user', [ClientController::class, 'getClientUser']);
            Route::post('/{clientId}/add-account', [ClientController::class, 'addAccount']);
            Route::get('/{client}/dettes', [DetteController::class, 'clientDettes']);
        });
        Route::apiResource('/clients', ClientController::class)->only(['index', 'store', 'show']);

        /**
         * Gestion des dettes
         */
        Route::prefix('dettes')->group(function () {
            Route::apiResource('/', DetteController::class);
            // Effectuer un paiement sur une dette
            Route::post('/{dette}/paiements', [DetteController::class, 'addPaiement']);
            //Récupère les détails d'une dette spécifique.
            Route::get('/{dette}', [DetteController::class, 'show']);

            // Nouvelles routes pour les filtres et les recherches avancées
             Route::get('/filter', [DetteController::class, 'index']);
             Route::get('/search', [DetteController::class, 'index']);
             // Filtre les dettes en fonction de l'ID client.
             Route::get('/filter/client/{clientId}', [DetteController::class, 'filterByClient']);
             // Filtre les dettes en fonction de la date de création.
             Route::get('/filter/date-range', [DetteController::class, 'filterByDateRange']);
             // Recherche les dettes en fonction du libellé.
             Route::get('/search/description', [DetteController::class, 'searchByDescription']);
             // Recherche les dettes en fonction du montant.
             Route::get('/filter/solde', [DetteController::class, 'filterBySolde']);
             // Recherche les dettes en fonction de la date de paiement.
             Route::get('/advanced-search', [DetteController::class, 'advancedSearch']);
        });

        /**
         * Gestion des paiements
         */
        Route::post('/dettes/{dette}/paiements', [PaiementController::class, 'store']);

        /**
         * Archivage des dettes
         */
        Route::prefix('archive')->group(function () {
            // Archiver toutes les dettes soldées
            Route::post('/dettes', [ArchiveController::class, 'archiveDettes']);
            // Voir les dettes archivées
            Route::get('/dettes', [ArchiveController::class, 'showArchivedDettes']);
            // Voir les détails d'une dette archivée
            Route::get('/dettes/{detteId}', [ArchiveController::class, 'showArchivedDetails']);
            // Voir les dettes archivées d'un client
            Route::get('/clients/{clientId}/dettes', [ArchiveController::class, 'showClientArchivedDettes']);
        });

        /**
         * Restauration des dettes
         */
        Route::prefix('restaure')->group(function () {
            // Récupère toutes les dettes restaurables
            Route::get('/', [ArchiveController::class, 'getRestorableDettes']);
            // Restaurer une dette spécifique
            Route::patch('/dette/{detteId}', [ArchiveController::class, 'restoreDette']);
            // Restaurer toutes les dettes d'un client
            Route::patch('/client/{clientId}', [ArchiveController::class, 'restoreClientDettes']);
        });

        /**
         * Gestion des articles
         */
        Route::prefix('articles')->group(function () {
            Route::apiResource('/', ArticleController::class);
            Route::post('/trashed', [ArticleController::class, 'trashed']);
            Route::patch('/{id}/restore', [ArticleController::class, 'restore']);
            Route::post('/libelle', [ArticleController::class, 'getByLibelle']);
            Route::delete('/{id}/force-delete', [ArticleController::class, 'forceDelete']);
            Route::post('/stock', [ArticleController::class, 'updateMultiple']);
        });

        /**
         * Tâches de fond (Jobs)
         */
        Route::prefix('jobs')->group(function () {
            Route::post('/archive', function () {
                ArchiveDettesPayees::dispatch();
                return response()->json(['message' => 'Archivage déclenché'], 200);
            });
            Route::post('/recap', function () {
                EnvoyerRecapitulatifHebdomadaire::dispatch();
                return response()->json(['message' => 'Envoi du récapitulatif déclenché'], 200);
            });
        });
    });
});
