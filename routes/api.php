<?php

use Illuminate\Http\Request;
use App\Jobs\ArchiveDettesPayees;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DetteController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;
use App\Jobs\EnvoyerRecapitulatifHebdomadaire;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ArchiveController;

Route::prefix('v1')->group(function () {
    // Routes d'authentification accessibles sans authentification
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // Routes protégées par auth:api et blacklisted
    Route::middleware(['auth:api', 'blacklisted'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Routes protégées pour les utilisateurs
        Route::apiResource('/users', UserController::class);

        // Routes pour les clients
        Route::post('/clients/telephone', [ClientController::class, 'getByPhoneNumber']);
        Route::apiResource('/clients', ClientController::class)->only(['index', 'store', 'show']);
        Route::post('/clients/register', [ClientController::class, 'addAccount']);
        Route::get('/clients/{id}/user', [ClientController::class, 'getClientUser']);
        Route::post('/clients/{clientId}/add-account', [ClientController::class, 'addAccount']);

        // Routes pour les dettes
        Route::apiResource('/dettes', DetteController::class);
        Route::post('/dettes/{dette}/paiements', [DetteController::class, 'addPaiement']);
        Route::get('/dettes/{dette}', [DetteController::class, 'show']);
        Route::get('/clients/{client}/dettes', [DetteController::class, 'clientDettes']);

        Route::get('/dettes/{dette}', [DetteController::class, 'show']);
        Route::get('/archives/{date}', [ArchiveController::class, 'show']);

        // Routes pour l'archivage
        Route::post('/archive-dettes', [ArchiveController::class, 'archiveDettes']);

        // Routes pour les paiments
        Route::post('/dettes/{dette}/paiements', [PaiementController::class, 'store']);

        // Routes pour les tâches de fond
        Route::post('/archive', function () {
            ArchiveDettesPayees::dispatch();
            return response()->json(['message' => 'Archivage déclenché'], 200);
        });

        Route::post('/recap', function () {
            EnvoyerRecapitulatifHebdomadaire::dispatch();
            return response()->json(['message' => 'Envoi du récapitulatif déclenché'], 200);
        });

        // Routes pour les articles
        Route::apiResource('/articles', ArticleController::class);
        Route::prefix('/articles')->group(function () {
            Route::post('/trashed', [ArticleController::class, 'trashed']);
            Route::patch('/{id}/restore', [ArticleController::class, 'restore']);
            Route::post('/libelle', [ArticleController::class, 'getByLibelle']);
            Route::delete('/{id}/force-delete', [ArticleController::class, 'forceDelete']);
            Route::post('/stock', [ArticleController::class, 'updateMultiple']);
        });
    });
});
