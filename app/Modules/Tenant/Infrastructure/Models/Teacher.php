<?php

namespace App\Modules\Tenant\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id_number',
        'specialization',
        'bio',
    ];

    public function user()
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function classes()
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'graded_by');
    }
}
