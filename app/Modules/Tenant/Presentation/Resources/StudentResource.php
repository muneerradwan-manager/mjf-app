<?php

namespace App\Modules\Tenant\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id_number' => $this->student_id_number,
            'date_of_birth' => $this->date_of_birth,
            'address' => $this->address,
            'phone' => $this->phone,
            'parent_name' => $this->parent_name,
            'parent_phone' => $this->parent_phone,
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'type' => $this->user->type,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
