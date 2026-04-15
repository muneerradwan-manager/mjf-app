<?php

namespace App\Modules\Central\Infrastructure\Models;

use App\Models\User;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    // Use integer auto-increment PK instead of UUID string.
    // GeneratesIds overrides getIncrementing()/getKeyType() as methods, so we must
    // override those methods explicitly (property overrides alone are not enough).
    public $incrementing = true;

    protected $keyType = 'int';

    public function getIncrementing()
    {
        return true;
    }

    public function getKeyType()
    {
        return 'int';
    }

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'code',
        'email',
        'phone',
        'subscription_id',
        'type',
        'db_name',
        'db_user',
        'db_password',
        'domain',
        'subdomain',
        'is_active',
        'owner_user_id',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'data' => 'array',
    ];

    /**
     * Tell VirtualColumn which attributes map to real DB columns.
     * Any attribute NOT listed here gets redirected to the `data` JSON column.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id', 'uuid', 'name', 'slug', 'code', 'email', 'phone',
            'subscription_id', 'type', 'db_name', 'db_user', 'db_password',
            'domain', 'subdomain', 'is_active', 'owner_user_id',
            'settings', 'data', 'created_at', 'updated_at',
        ];
    }

    /**
     * Map stancl's internal 'db_name' key to our actual db_name column,
     * so DatabaseConfig::getName() uses our stored value.
     */
    public function getInternal(string $key)
    {
        if ($key === 'db_name') {
            return $this->db_name;
        }

        return parent::getInternal($key);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
