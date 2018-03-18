<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(config('api.cors.status')) {
            // Cors headers enabled
            return $next($request)
                ->header('Access-Control-Allow-Origin', config('api.cors.allow.origin'))
                ->header('Access-Control-Allow-Methods', config('api.cors.allow.methods'))
                ->header('Access-Control-Allow-Headers', config('api.cors.allow.headers'));
        }

        return $next($request);
    }
}
