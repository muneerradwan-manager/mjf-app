<?php

namespace App\Modules\Tenant\Presentation\Requests;

use App\Modules\Tenant\Infrastructure\Models\Student;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $student = Student::query()->findOrFail((int) $this->route('student'));

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($student->user_id),
            ],
            'password' => 'nullable|string|min:8',
            'student_id_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('students', 'student_id_number')->ignore($student->id),
            ],
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:255',
        ];
    }
}
