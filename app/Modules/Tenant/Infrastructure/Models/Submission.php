<?php

namespace App\Modules\Tenant\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'submission_date',
        'file_path',
        'content',
        'grade',
        'feedback',
    ];

    protected $casts = [
        'submission_date' => 'datetime',
        'grade' => 'decimal:2',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
