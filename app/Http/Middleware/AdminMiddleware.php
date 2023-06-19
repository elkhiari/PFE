<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role == 'admin') {
            return $next($request);
        }

        return response()->json(['error' => 'You are not authorized to access this resource.'], 401);
    }
}
