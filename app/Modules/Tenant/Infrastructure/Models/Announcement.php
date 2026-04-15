<?php

namespace App\Modules\Tenant\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'created_by',
        'audience_type',
        'audience_id',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(TenantUser::class, 'created_by');
    }
}
