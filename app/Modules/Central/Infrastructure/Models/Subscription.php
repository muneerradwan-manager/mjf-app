<?php

namespace App\Modules\Central\Infrastructure\Models;

use App\Shared\Infrastructure\Concerns\UsesCentralConnection;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use UsesCentralConnection;

    protected $fillable = [
        'title',
        'description',
        'price',
        'currency',
        'duration_in_days',
        'billing_period',
        'features',
        'status',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
}
