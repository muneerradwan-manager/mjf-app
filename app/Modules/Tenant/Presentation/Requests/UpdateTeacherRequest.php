<?php

namespace App\Modules\Tenant\Presentation\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Tenant\Infrastructure\Models\Teacher;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $teacher = Teacher::query()->findOrFail((int) $this->route('teacher'));

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($teacher->user_id),
            ],
            'password' => 'nullable|string|min:8',
            'employee_id_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('teachers', 'employee_id_number')->ignore($teacher->id),
            ],
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ];
    }
}
