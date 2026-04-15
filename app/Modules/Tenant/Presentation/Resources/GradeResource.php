<?php

namespace App\Modules\Tenant\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'grade' => $this->grade,
            'comments' => $this->comments,
            'student' => $this->whenLoaded('student', fn () => $this->student ? [
                'id' => $this->student->id,
                'student_id_number' => $this->student->student_id_number,
                'user' => $this->student->relationLoaded('user') && $this->student->user ? [
                    'id' => $this->student->user->id,
                    'name' => $this->student->user->name,
                    'email' => $this->student->user->email,
                ] : null,
            ] : null),
            'course' => $this->whenLoaded('course', fn () => $this->course ? [
                'id' => $this->course->id,
                'name' => $this->course->name,
                'code' => $this->course->code,
            ] : null),
            'assignment' => $this->whenLoaded('assignment', fn () => $this->assignment ? [
                'id' => $this->assignment->id,
                'title' => $this->assignment->title,
                'class' => $this->assignment->relationLoaded('classroom') && $this->assignment->classroom ? [
                    'id' => $this->assignment->classroom->id,
                    'name' => $this->assignment->classroom->name,
                    'course' => $this->assignment->classroom->relationLoaded('course') && $this->assignment->classroom->course ? [
                        'id' => $this->assignment->classroom->course->id,
                        'name' => $this->assignment->classroom->course->name,
                        'code' => $this->assignment->classroom->course->code,
                    ] : null,
                ] : null,
            ] : null),
            'teacher' => $this->whenLoaded('teacher', fn () => $this->teacher ? [
                'id' => $this->teacher->id,
                'employee_id_number' => $this->teacher->employee_id_number,
                'user' => $this->teacher->relationLoaded('user') && $this->teacher->user ? [
                    'id' => $this->teacher->user->id,
                    'name' => $this->teacher->user->name,
                    'email' => $this->teacher->user->email,
                ] : null,
            ] : null),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
