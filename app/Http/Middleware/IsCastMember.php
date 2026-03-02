<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsCastMember
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user || !$user->hasAnyRole(['respondent', 'admin'])) {
            abort(403, 'Access denied. Respondent privileges required.');
        }
        return $next($request);
    }
}
