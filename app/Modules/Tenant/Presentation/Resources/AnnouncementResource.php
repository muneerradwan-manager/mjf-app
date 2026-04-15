<?php

namespace App\Modules\Tenant\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'content'       => $this->content,
            'audience_type' => $this->audience_type,
            'audience_id'   => $this->audience_id,
            'published_at'  => $this->published_at,
            'creator'       => $this->whenLoaded('creator', fn () => $this->creator ? [
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
