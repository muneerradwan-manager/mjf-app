<?php

namespace App\Modules\Tenant\Presentation\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Tenant\Infrastructure\Models\Course;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $course = Course::query()->findOrFail((int) $this->route('course'));

        return [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses', 'code')->ignore($course->id),
            ],
            'description' => 'nullable|string',
            'teacher_id' => 'nullable|integer|exists:teachers,id',
            'status' => 'required|in:active,inactive,archived',
        ];
    }
}
