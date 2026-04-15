<?php

namespace App\Modules\Tenant\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'assignment_id',
        'grade',
        'comments',
        'graded_by',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'graded_by');
    }
}
