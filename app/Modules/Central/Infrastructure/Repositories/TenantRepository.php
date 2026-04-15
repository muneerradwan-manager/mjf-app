<?php

namespace App\Modules\Central\Infrastructure\Repositories;

use App\Modules\Central\Domain\Repositories\TenantRepositoryInterface;
use App\Modules\Central\Infrastructure\Models\Tenant;
use App\Shared\Infrastructure\Repositories\BaseRepository;

class TenantRepository extends BaseRepository implements TenantRepositoryInterface
{
    public function __construct(Tenant $model)
    {
        $this->model = $model;
    }
}
