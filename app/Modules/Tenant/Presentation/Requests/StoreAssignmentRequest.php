<?php

namespace App\Modules\Tenant\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'class_id' => 'required|integer|exists:classes,id',
            'teacher_id' => 'required|integer|exists:teachers,id',
            'max_grade' => 'required|numeric|min:0.01',
        ];
    }
}
