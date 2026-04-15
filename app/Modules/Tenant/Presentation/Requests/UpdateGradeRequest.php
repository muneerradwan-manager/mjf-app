<?php

namespace App\Modules\Tenant\Presentation\Requests;

use App\Modules\Tenant\Infrastructure\Models\Grade;

class UpdateGradeRequest extends StoreGradeRequest
{
    public function rules(): array
    {
        Grade::query()->findOrFail((int) $this->route('grade'));

        return parent::rules();
    }

    public function after(): array
    {
        return [
            fn ($validator) => $this->validateGradePayload($validator, (int) $this->route('grade')),
        ];
    }
}
