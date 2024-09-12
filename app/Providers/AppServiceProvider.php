<?php

namespace App\Providers;

use App\Models\Client;
use App\Services\ClientService;
use App\Services\ArchiveService;
use App\Services\ArticleService;
use App\Services\MessageService;
use App\Observers\ClientObserver;
use App\Repositories\UserRepository;
use App\Facades\ClientObserverFacade;
use App\Repositories\ClientRepository;
use App\Repositories\UploadRepository;
use App\Repositories\ArticleRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\NexmoMessageRepository;
use App\Repositories\TwilioMessageRepository;
use App\Repositories\VonageMessageRepository;
use App\Repositories\MongoDBArchiveRepository;
use App\Repositories\FirebaseArchiveRepository;
use App\Repositories\SMSGlobalMessageRepository;
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

        // Configuration du repository d'archivage
        $this->app->bind(ArchiveRepositoryInterface::class, function ($app) {
            $driver = config('archive.driver', 'firebase');
            switch ($driver) {
                case 'mongodb':
                    return new MongoDBArchiveRepository();
                case 'firebase':
                default:
                    return new FirebaseArchiveRepository();
            }
        });

        // Configuration du repository de messagerie
        $this->app->bind(MessageRepositoryInterface::class, function ($app) {
            $driver = config('message.driver', 'twilio');
            switch ($driver) {
                case 'vonage':
                    return new VonageMessageRepository();
                case 'twilio':
                default:
                    return new TwilioMessageRepository();
            }
        });

        $this->app->bind(ArchiveService::class, function ($app) {
            return new ArchiveService($app->make(ArchiveRepositoryInterface::class));
        });

        $this->app->bind(MessageService::class, function ($app) {
            return new MessageService($app->make(MessageRepositoryInterface::class));
        });
    }

    public function boot()
    {
        ClientObserverFacade::register();
        Client::observe(ClientObserver::class);
    }
}
