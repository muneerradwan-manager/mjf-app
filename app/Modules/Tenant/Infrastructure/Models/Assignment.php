<?php

namespace App\Modules\Tenant\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'class_id',
        'teacher_id',
        'max_grade',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'max_grade' => 'decimal:2',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
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
