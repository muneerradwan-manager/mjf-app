<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Central\Infrastructure\Models\Tenant;
use App\Modules\Tenant\Infrastructure\Models\TenantUser;
use App\Modules\Tenant\Infrastructure\Models\Teacher;
use App\Modules\Tenant\Infrastructure\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('api_token')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // ── 1. Try central user (super admin / tenant owner) ──────────────────
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            return $this->completeCentralLogin($user);
        }

        // ── 2. Try tenant databases (admin / teacher / student) ───────────────
        $found = $this->findInTenantDatabases($request->email, $request->password);

        if ($found) {
            return $this->completeTenantUserLogin(
                $found['userData'],
                $found['tenant'],
                $found['profileId']
            );
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout(Request $request)
    {
        $token = session('api_token');

        if ($token) {
            $user = User::whereHas('tokens', function ($q) use ($token) {
                $q->where('token', hash('sha256', explode('|', $token, 2)[1] ?? $token));
            })->first();

            $user?->tokens()->where('name', 'web-session')->delete();
        }

        $request->session()->flush();

        return redirect()->route('login');
    }

    public function switchTenantWeb(Request $request)
    {
        $request->validate(['tenant_id' => 'required|integer']);

        $user = User::find(session('user.id'));
        if (!$user) return back();

        $this->switchTenantForUser($user, session('api_token'), (int) $request->tenant_id);

        return back();
    }

    // ─── Tenant database search ───────────────────────────────────────────────

    private function findInTenantDatabases(string $email, string $password): ?array
    {
        foreach (Tenant::all() as $tenant) {
            tenancy()->initialize($tenant);

            try {
                $tenantUser = TenantUser::where('email', $email)->first();

                if (!$tenantUser || !Hash::check($password, $tenantUser->password)) {
                    continue;
                }

                // Capture data while tenant DB is active
                $userData = [
                    'id'       => $tenantUser->id,
                    'name'     => $tenantUser->name,
                    'email'    => $tenantUser->email,
                    'type'     => $tenantUser->type,
                    'password' => $tenantUser->getAttributes()['password'],
                ];

                $profileId = null;
                if ($tenantUser->type === 'teacher') {
                    $profileId = Teacher::where('user_id', $tenantUser->id)->value('id');
                } elseif ($tenantUser->type === 'student') {
                    $profileId = Student::where('user_id', $tenantUser->id)->value('id');
                }

                return ['userData' => $userData, 'tenant' => $tenant, 'profileId' => $profileId];

            } finally {
                tenancy()->end();
            }
        }

        return null;
    }

    // ─── Login completions ────────────────────────────────────────────────────

    private function completeCentralLogin(User $user): RedirectResponse
    {
        $token = $user->createToken('web-session')->plainTextToken;
        $user->loadMissing(['currentTenant', 'tenants']);
        $serialized = $this->serializeUser($user);

        $role = $user->is_super_admin ? 'super_admin' : 'owner';

        session([
            'api_token'         => $token,
            'user'              => $serialized,
            'current_tenant'    => $serialized['current_tenant'],
            'tenants'           => $serialized['tenants'],
            'user_role'         => $role,
            'tenant_profile_id' => null,
            'tenant_user_id'    => null,
        ]);

        // Auto-select first accessible tenant if none is active
        if (empty($serialized['current_tenant'])) {
            $firstTenantId = !empty($serialized['tenants'])
                ? $serialized['tenants'][0]['id']
                : ($user->is_super_admin ? Tenant::value('id') : null);

            if ($firstTenantId) {
                $this->switchTenantForUser($user, $token, $firstTenantId);
            }
        }

        return redirect()->intended(route('dashboard'));
    }

    private function completeTenantUserLogin(array $userData, Tenant $tenant, ?int $profileId): RedirectResponse
    {
        // Find or create a central proxy User for this tenant user
        $centralUser = User::firstOrCreate(
            ['email' => $userData['email']],
            [
                'name'           => $userData['name'],
                'password'       => $userData['password'], // already hashed
                'is_super_admin' => false,
            ]
        );

        // Keep name in sync
        if ($centralUser->name !== $userData['name']) {
            $centralUser->forceFill(['name' => $userData['name']])->save();
        }

        // Attach to tenant with role if not already attached
        if (!$centralUser->tenants()->whereKey($tenant->id)->exists()) {
            $centralUser->tenants()->attach($tenant->id, ['role' => $userData['type']]);
        }

        // Set current tenant
        $centralUser->forceFill(['current_tenant_id' => $tenant->id])->save();
        $centralUser = $centralUser->fresh();

        $token      = $centralUser->createToken('web-session')->plainTextToken;
        $serialized = $this->serializeUser($centralUser);

        session([
            'api_token'         => $token,
            'user'              => $serialized,
            'current_tenant'    => $serialized['current_tenant'],
            'tenants'           => $serialized['tenants'],
            'user_role'         => $userData['type'],   // 'admin'|'teacher'|'student'
            'tenant_profile_id' => $profileId,          // Teacher.id or Student.id
            'tenant_user_id'    => $userData['id'],     // TenantUser.id in tenant DB
        ]);

        return redirect()->intended(route('dashboard'));
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function switchTenantForUser(User $user, string $token, int $tenantId): void
    {
        $hasAccess = $user->is_super_admin
            || $user->tenants()->whereKey($tenantId)->exists()
            || Tenant::whereKey($tenantId)->where('owner_user_id', $user->id)->exists();

        if (!$hasAccess) return;

        $user->forceFill(['current_tenant_id' => $tenantId])->save();
        $serialized = $this->serializeUser($user->fresh());

        session([
            'user'           => $serialized,
            'current_tenant' => $serialized['current_tenant'],
        ]);
    }

    private function serializeUser(User $user): array
    {
        $user->loadMissing(['currentTenant', 'tenants']);

        $tenants = $user->is_super_admin
            ? Tenant::orderBy('name')->get()->map(fn($t) => [
                'id'   => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
                'role' => 'super_admin',
            ])->values()->all()
            : $user->tenants->map(fn($t) => [
                'id'   => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
                'role' => $t->pivot?->role,
            ])->values()->all();

        return [
            'id'                => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'is_super_admin'    => $user->is_super_admin,
            'current_tenant_id' => $user->current_tenant_id,
            'current_tenant'    => $user->currentTenant ? [
                'id'   => $user->currentTenant->id,
                'name' => $user->currentTenant->name,
                'slug' => $user->currentTenant->slug,
            ] : null,
            'tenants' => $tenants,
        ];
    }
}
