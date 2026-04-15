<?php

namespace App\Modules\Central\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // later we secure it
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:tenants,email',
            'subscription_id' => 'required|exists:subscriptions,id',
            'type'            => 'required|in:masjed,school,university',
            'owner_user_id'   => 'required|exists:users,id',
        ];
    }
}
