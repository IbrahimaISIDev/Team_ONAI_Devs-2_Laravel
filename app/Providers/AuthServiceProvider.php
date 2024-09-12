<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Dette;
use App\Models\Client;
use App\Models\Article;
use App\Policies\UserPolicy;
use App\Policies\DettePolicy;
use App\Policies\ClientPolicy;
use Laravel\Passport\Passport;
use App\Policies\ArticlePolicy;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\AuthorizationController;
use Laravel\Passport\Http\Controllers\TransientTokenController;
use Laravel\Passport\Http\Controllers\PersonalAccessTokenController;
use Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Client::class => ClientPolicy::class,
        Article::class => ArticlePolicy::class,
        Dette::class => DettePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Passport::loadKeysFrom(_DIR_.'/../secrets/oauth');
        /*  passport::hashClientSecrets();
         Passport::tokensExpireIn(now()->second(60));
         Passport::refreshTokensExpireIn(now()->addDays(30)); 
         Passport::personalAccessTokensExpireIn(now()->addMonths(6)); */
        /* $this->registerPolicies(); */
        $this->registerPolicies();

        Passport::tokensExpireIn(now()->addMinutes(15)); // Ajustez la durée d'expiration des tokens d'accès
        Passport::refreshTokensExpireIn(now()->addDays(7)); // Ajustez la durée d'expiration des refresh tokens
        Passport::personalAccessTokensExpireIn(now()->addMonths(1)); // Ajustez la durée d'expiration des tokens d'accès personnels
    }
}
