<?php

namespace App\Modules\Tenant\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Tenant\Infrastructure\Models\Classroom;

class UpdateClassroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        Classroom::query()->findOrFail((int) $this->route('class'));

        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|integer|exists:courses,id',
            'teacher_id' => 'required|integer|exists:teachers,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'schedule' => 'nullable|array',
        ];
    }
}
