<?php

namespace App\Modules\Tenant\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'start_date'  => $this->start_date,
            'end_date'    => $this->end_date,
            'location'    => $this->location,
            'creator'     => $this->whenLoaded('creator', fn () => $this->creator ? [
                'id'    => $this->creator->id,
                'name'  => $this->creator->name,
                'email' => $this->creator->email,
                'type'  => $this->creator->type,
            ] : null),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
