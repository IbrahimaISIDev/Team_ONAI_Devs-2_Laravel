<?php

namespace App\Providers;

use App\Models\Client;
use Barryvdh\DomPDF\PDF;
use App\Services\ArchiveService;
use App\Services\ArticleService;
use App\Services\FactureService;
use App\Services\MessageService;
use App\Observers\ClientObserver;
use App\Repositories\UserRepository;
use App\Facades\ClientObserverFacade;
use App\Services\CloudStorageService;
use App\Repositories\ClientRepository;
use App\Repositories\UploadRepository;
use App\Repositories\ArchiveRepository;
use App\Repositories\ArticleRepository;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\CloudStorageInterface;
use App\Interfaces\MessageServiceInterface;
use App\Repositories\TwilioMessageRepository;
use App\Repositories\InfobipMessageRepository;
use App\Repositories\MongoDBArchiveRepository;
use App\Repositories\FirebaseArchiveRepository;
use App\Services\Interfaces\ArticleServiceInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\ClientRepositoryInterface;
use App\Repositories\Interfaces\UploadRepositoryInterface;
use App\Repositories\Interfaces\ArchiveRepositoryInterface;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Repositories\Interfaces\MessageRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ArticleRepositoryInterface::class, ArticleRepository::class);
        $this->app->bind(ArticleServiceInterface::class, ArticleService::class);
        $this->app->bind(UploadRepositoryInterface::class, UploadRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(CloudStorageInterface::class, CloudStorageService::class);
        $this->app->bind(MessageServiceInterface::class, MessageService::class);

        // Configuration du repository d'archivage
        $this->app->singleton(ArchiveRepositoryInterface::class, function ($app) {
            $driver = config('archive.driver', 'mongodb');
            return $driver === 'mongodb' ? new MongoDBArchiveRepository() : new FirebaseArchiveRepository();
        });

        $this->app->singleton('archiveRepository', function ($app) {
            return new ArchiveRepository($app->make(ArchiveRepositoryInterface::class));
        });

        // Configuration du repository de messagerie
        $this->app->bind(MessageRepositoryInterface::class, function ($app) {
            $driver = config('message.driver', 'twilio');
            return $driver === 'infobip' ? new InfobipMessageRepository() : new TwilioMessageRepository();
        });

        $this->app->bind(ArchiveService::class, function ($app) {
            return new ArchiveService(
                $app->make(ArchiveRepositoryInterface::class),
                $app->make(CloudStorageInterface::class) // Ajout de la dépendance manquante
            );
        });

        $this->app->bind(MessageService::class, function ($app) {
            return new MessageService(
                $app->make(MessageRepositoryInterface::class),
                $app->make(FactureService::class) // Inject FactureService
            );
        });

        $this->app->singleton(FactureService::class, function ($app) {
            return new FactureService($app->make(PDF::class));
        });

        $this->app->singleton(PDF::class, function ($app) {
            return $app->make('dompdf.wrapper');
        });
    }

    public function boot()
    {
        ClientObserverFacade::register();
        Client::observe(ClientObserver::class);
    }
}
