<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && !$user->is_active) {
            return \App\Traits\ApiResponse::error(message: 'Your account is inactive. Please contact support.', statusCode: 403);
        }
        return $next($request);
    }
}
