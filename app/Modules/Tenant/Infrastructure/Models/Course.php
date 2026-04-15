<?php

namespace App\Modules\Tenant\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'teacher_id',
        'status',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classes()
    {
        return $this->hasMany(Classroom::class, 'course_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
