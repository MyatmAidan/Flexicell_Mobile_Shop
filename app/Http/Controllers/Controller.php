<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Check if the current user has a specific permission.
     * Aborts with 403 if not.
     */
    protected function requirePermission(string $permission): void
    {
        $user = Auth::user();

        if (!$user || !$user->hasPermissionTo($permission)) {
            abort(403, 'You do not have permission to perform this action.');
        }
    }
}
