<?php

namespace App\Models;

use App\Shared\Infrastructure\Concerns\UsesCentralConnection;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Modules\Central\Infrastructure\Models\Tenant;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, UsesCentralConnection;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
        'current_tenant_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_super_admin' => 'boolean',
    ];

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class)->withPivot('role');
    }

    public function currentTenant()
    {
        return $this->belongsTo(Tenant::class, 'current_tenant_id');
    }

    public function ownedTenants()
    {
        return $this->hasMany(Tenant::class, 'owner_user_id');
    }
}
