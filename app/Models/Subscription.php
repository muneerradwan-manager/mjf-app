<?php

namespace App\Models;

use App\Shared\Infrastructure\Concerns\UsesCentralConnection;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use UsesCentralConnection;

    protected $fillable = [
        'title',
        'price',
        'currency',
        'duration_in_days',
        'billing_period',
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
