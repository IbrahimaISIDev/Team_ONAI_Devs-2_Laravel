<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Client;
use App\Observers\ClientObserver;

class ObserveClientMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        Client::observe(ClientObserver::class);

        return $response;
    }
}