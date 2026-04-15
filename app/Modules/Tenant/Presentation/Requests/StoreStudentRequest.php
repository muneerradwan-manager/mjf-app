<?php

namespace App\Modules\Tenant\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'student_id_number' => 'nullable|string|max:255|unique:students,student_id_number',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:255',
        ];
    }
}
