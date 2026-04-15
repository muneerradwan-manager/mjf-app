<?php

namespace App\Modules\Tenant\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'status' => $this->status,
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
