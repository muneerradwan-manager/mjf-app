<?php

namespace App\Modules\Tenant\Presentation\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
                Rule::unique('enrollments', 'student_id')
                    ->where(fn ($query) => $query->where('class_id', $this->integer('class_id'))),
            ],
            'class_id' => 'required|integer|exists:classes,id',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,completed,dropped',
        ];
    }
}
