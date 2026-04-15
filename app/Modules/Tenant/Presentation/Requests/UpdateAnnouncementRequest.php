<?php

namespace App\Modules\Tenant\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title'         => 'required|string|max:255',
            'content'       => 'required|string',
            'created_by'    => 'required|integer|exists:users,id',
            'audience_type' => 'nullable|in:all,students,teachers,class',
            'audience_id'   => 'nullable|integer',
            'published_at'  => 'nullable|date',
        ];
    }
}
