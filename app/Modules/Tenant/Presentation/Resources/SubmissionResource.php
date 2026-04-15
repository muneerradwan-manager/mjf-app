<?php

namespace App\Modules\Tenant\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'submission_date' => $this->submission_date,
            'file_path' => $this->file_path,
            'content' => $this->content,
            'grade' => $this->grade,
            'feedback' => $this->feedback,
            'assignment' => $this->whenLoaded('assignment', fn () => $this->assignment ? [
                'id' => $this->assignment->id,
                'title' => $this->assignment->title,
                'max_grade' => $this->assignment->max_grade,
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
            'student' => $this->whenLoaded('student', fn () => $this->student ? [
                'id' => $this->student->id,
                'student_id_number' => $this->student->student_id_number,
                'user' => $this->student->relationLoaded('user') && $this->student->user ? [
                    'id' => $this->student->user->id,
                    'name' => $this->student->user->name,
                    'email' => $this->student->user->email,
                ] : null,
            ] : null),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
