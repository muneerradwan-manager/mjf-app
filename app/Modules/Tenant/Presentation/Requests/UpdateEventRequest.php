<?php

namespace App\Modules\Tenant\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'location'    => 'nullable|string|max:255',
            'created_by'  => 'required|integer|exists:users,id',
        ];
    }
}
