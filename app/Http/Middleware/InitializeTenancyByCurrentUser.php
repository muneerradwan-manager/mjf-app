<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Modules\Central\Infrastructure\Models\Tenant;

class InitializeTenancyByCurrentUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (! $user->current_tenant_id) {
            return response()->json([
                'status' => false,
                'message' => 'No active tenant selected. Use /api/central/current-tenant first.',
            ], 409);
        }

        $tenant = Tenant::query()->find($user->current_tenant_id);

        if (! $tenant || ! $tenant->is_active) {
            return response()->json([
                'status' => false,
                'message' => 'The active tenant is unavailable or inactive.',
            ], 404);
        }

        tenancy()->initialize($tenant);

        try {
            return $next($request);
        } finally {
            tenancy()->end();
        }
    }
}
