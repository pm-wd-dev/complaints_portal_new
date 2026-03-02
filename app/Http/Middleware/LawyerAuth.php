<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LawyerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if lawyer is authenticated
        if (!session('lawyer_authenticated') || !session('lawyer_user_id')) {
            return redirect()->route('lawyer.login')
                ->with('error', 'Please log in to access this page.');
        }

        // Optional: Check if access token is still valid
        $accessToken = session('lawyer_access_token');
        if ($accessToken) {
            $access = \App\Models\LawyerAccess::where('access_token', $accessToken)
                ->where('expires_at', '>', now())
                ->first();
                
            if ($access) {
                $access->updateLastAccess();
            }
        }

        return $next($request);
    }
}