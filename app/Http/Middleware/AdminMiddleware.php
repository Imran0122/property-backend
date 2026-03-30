<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $isAdmin = ((int) ($user->is_admin ?? 0) === 1)
            || (strtolower((string) ($user->role ?? '')) === 'admin');

        if (!$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        return $next($request);
    }
}