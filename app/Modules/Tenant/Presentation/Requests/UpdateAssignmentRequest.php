<?php

namespace App\Modules\Tenant\Presentation\Requests;

use App\Modules\Tenant\Infrastructure\Models\Assignment;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        Assignment::query()->findOrFail((int) $this->route('assignment'));

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
