<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireDemoVerifier
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('demo.verifier.active')) {
            return to_route('demo.verifier.login');
        }

        return $next($request);
    }
}
