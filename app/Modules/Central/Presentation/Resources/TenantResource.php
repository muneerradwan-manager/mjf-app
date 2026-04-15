<?php

namespace App\Modules\Central\Presentation\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
