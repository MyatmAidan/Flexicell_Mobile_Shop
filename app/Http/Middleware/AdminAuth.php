<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * Allow any of the three admin-panel roles: superadmin, manager, staff.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['superadmin', 'manager', 'staff'])) {
            return redirect()->route('home')->with('error', 'Unauthorized action. Admin access required.');
        }

        return $next($request);
    }
}


