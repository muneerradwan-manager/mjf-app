<?php

namespace App\Modules\Tenant\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
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
            'employee_id_number' => 'nullable|string|max:255|unique:teachers,employee_id_number',
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ];
    }
}
