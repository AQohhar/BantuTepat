<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireDemoOperator
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('demo.operator.active')) {
            return to_route('demo.operator.login');
        }

        return $next($request);
    }
}
