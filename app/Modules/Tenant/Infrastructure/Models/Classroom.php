<?php

namespace App\Modules\Tenant\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'description',
        'course_id',
        'teacher_id',
        'start_date',
        'end_date',
        'schedule',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'schedule' => 'array',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'class_id');
    }
}
