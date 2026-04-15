<?php

namespace App\Modules\Tenant\Presentation\Requests;

use App\Modules\Tenant\Infrastructure\Models\Enrollment;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $enrollment = Enrollment::query()->findOrFail((int) $this->route('enrollment'));

        return [
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
                Rule::unique('enrollments', 'student_id')
                    ->ignore($enrollment->id)
                    ->where(fn ($query) => $query->where('class_id', $this->integer('class_id'))),
            ],
            'class_id' => 'required|integer|exists:classes,id',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,completed,dropped',
        ];
    }
}
