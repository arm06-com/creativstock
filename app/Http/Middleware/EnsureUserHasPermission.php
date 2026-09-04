<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = Auth::user();

        if (! $user || ! $user->hasPermission($permission)) {
            abort(403);
        }

        return $next($request);
    }
}
