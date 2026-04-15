<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveTenantAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $tenant = tenant();

        if (! $user || ! $tenant) {
            return response()->json([
                'status' => false,
                'message' => 'Tenant context is not available.',
            ], 400);
        }

        $hasAccess = $user->is_super_admin
            || (int) $tenant->owner_user_id === (int) $user->id
            || $user->tenants()->whereKey($tenant->getKey())->exists();

        if (! $hasAccess) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have access to the active tenant.',
            ], 403);
        }

        return $next($request);
    }
}
