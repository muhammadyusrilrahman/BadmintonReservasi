<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Check if user has any of the specified roles
        foreach ($roles as $role) {
            if ($request->user()->hasRole($role)) {
                return $next($request);
            }
        }

        // If JSON request, return 403
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk halaman ini.',
            ], 403);
        }

        // Redirect to appropriate dashboard based on user's role
        $dashboardRoute = $request->user()->getDashboardRoute();

        return redirect()->route($dashboardRoute)
            ->with('error', 'Anda tidak memiliki akses untuk halaman tersebut.');
    }
}
