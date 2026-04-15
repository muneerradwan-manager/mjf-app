<?php

namespace App\Modules\Central\Presentation\Controllers;

use App\Shared\Presentation\Controllers\BaseController;
use App\Modules\Central\Application\Services\TenantService;
use App\Modules\Central\Presentation\Requests\StoreTenantRequest;
use App\Modules\Central\Presentation\Resources\TenantResource;

use App\Modules\Central\Application\Services\TenantProvisionService;

class TenantController extends BaseController
{
    public function __construct(
        private TenantProvisionService $service
    ) {}

    public function store(StoreTenantRequest $request)
    {
        $tenant = $this->service->createTenantWithDatabase(
            $request->validated()
        );

        return $this->success($tenant, 'Tenant created with DB');
    }
}
