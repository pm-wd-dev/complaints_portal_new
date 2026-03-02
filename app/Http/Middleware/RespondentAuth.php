<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RespondentAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if respondent is authenticated
        if (!session('respondent_authenticated') || !session('respondent_user_id')) {
            return redirect()->route('respondent.login')
                ->with('error', 'Please log in to access this page.');
        }

        // Optional: Check if access token is still valid
        $accessToken = session('respondent_access_token');
        if ($accessToken) {
            $access = \App\Models\RespondentAccess::where('access_token', $accessToken)
                ->where('expires_at', '>', now())
                ->first();
                
            if ($access) {
                $access->updateLastAccess();
            }
        }

        return $next($request);
    }
}
