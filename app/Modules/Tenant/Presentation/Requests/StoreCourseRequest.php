<?php

namespace App\Modules\Tenant\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:courses,code',
            'description' => 'nullable|string',
            'teacher_id' => 'nullable|integer|exists:teachers,id',
            'status' => 'required|in:active,inactive,archived',
        ];
    }
}
