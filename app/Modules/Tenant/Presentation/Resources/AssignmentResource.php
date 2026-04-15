<?php

namespace App\Modules\Tenant\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'max_grade' => $this->max_grade,
            'class' => $this->whenLoaded('classroom', fn () => $this->classroom ? [
                'id' => $this->classroom->id,
                'name' => $this->classroom->name,
                'course' => $this->classroom->relationLoaded('course') && $this->classroom->course ? [
                    'id' => $this->classroom->course->id,
                    'name' => $this->classroom->course->name,
                    'code' => $this->classroom->course->code,
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
