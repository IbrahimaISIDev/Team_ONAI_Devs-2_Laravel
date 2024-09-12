<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Client;
use App\Observers\ClientObserver;

class RegisterObserversMiddleware
{
    public function handle($request, Closure $next)
    {
        Client::observe(ClientObserver::class);
        return $next($request);
    }
}