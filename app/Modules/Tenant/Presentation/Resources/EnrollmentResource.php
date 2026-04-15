<?php

namespace App\Modules\Tenant\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'enrollment_date' => $this->enrollment_date,
            'status' => $this->status,
            'student' => $this->whenLoaded('student', fn () => $this->student ? [
                'id' => $this->student->id,
                'student_id_number' => $this->student->student_id_number,
                'user' => $this->student->relationLoaded('user') && $this->student->user ? [
                    'id' => $this->student->user->id,
                    'name' => $this->student->user->name,
                    'email' => $this->student->user->email,
                ] : null,
            ] : null),
            'class' => $this->whenLoaded('classroom', fn () => $this->classroom ? [
                'id' => $this->classroom->id,
                'name' => $this->classroom->name,
                'course' => $this->classroom->relationLoaded('course') && $this->classroom->course ? [
                    'id' => $this->classroom->course->id,
                    'name' => $this->classroom->course->name,
                    'code' => $this->classroom->course->code,
                ] : null,
                'teacher' => $this->classroom->relationLoaded('teacher') && $this->classroom->teacher ? [
                    'id' => $this->classroom->teacher->id,
                    'name' => $this->classroom->teacher->relationLoaded('user') && $this->classroom->teacher->user
                        ? $this->classroom->teacher->user->name
                        : null,
                ] : null,
            ] : null),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
