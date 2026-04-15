<?php

namespace App\Modules\Central\Presentation\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Shared\Presentation\Controllers\BaseController;
use App\Modules\Central\Infrastructure\Models\Tenant;
use App\Modules\Central\Presentation\Requests\LoginRequest;
use App\Modules\Central\Presentation\Requests\SetCurrentTenantRequest;

class AuthController extends BaseController
{
    public function login(LoginRequest $request)
    {
        $user = User::query()
            ->where('email', $request->string('email'))
            ->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return $this->error('Invalid credentials.', 401);
        }

        $token = $user->createToken($request->userAgent() ?: 'api-token')->plainTextToken;

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->serializeUser($user->fresh()),
        ], 'Login successful');
    }

    public function me(Request $request)
    {
        return $this->success(
            $this->serializeUser($request->user()),
            'Authenticated user'
        );
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->success(null, 'Logout successful');
    }

    public function setCurrentTenant(SetCurrentTenantRequest $request)
    {
        $user = $request->user();
        $tenantId = (int) $request->integer('tenant_id');

        $hasAccess = $user->is_super_admin
            || $user->tenants()->whereKey($tenantId)->exists()
            || Tenant::query()
                ->whereKey($tenantId)
                ->where('owner_user_id', $user->id)
                ->exists();

        if (! $hasAccess) {
            return $this->error('You do not have access to the selected tenant.', 403);
        }

        $user->forceFill([
            'current_tenant_id' => $tenantId,
        ])->save();

        return $this->success(
            $this->serializeUser($user->fresh()),
            'Current tenant updated'
        );
    }

    protected function serializeUser(User $user): array
    {
        $user->loadMissing(['currentTenant', 'tenants']);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_super_admin' => $user->is_super_admin,
            'current_tenant_id' => $user->current_tenant_id,
            'current_tenant' => $user->currentTenant ? [
                'id' => $user->currentTenant->id,
                'name' => $user->currentTenant->name,
                'slug' => $user->currentTenant->slug,
            ] : null,
            'tenants' => $user->tenants->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'slug' => $tenant->slug,
                    'role' => $tenant->pivot?->role,
                ];
            })->values()->all(),
        ];
    }
}
