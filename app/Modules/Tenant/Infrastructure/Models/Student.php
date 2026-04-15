<?php

namespace App\Modules\Tenant\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_id_number',
        'date_of_birth',
        'address',
        'phone',
        'parent_name',
        'parent_phone',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
