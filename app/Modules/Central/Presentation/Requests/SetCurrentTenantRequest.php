<?php

namespace App\Modules\Central\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetCurrentTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => 'required|integer|exists:tenants,id',
        ];
    }
}
