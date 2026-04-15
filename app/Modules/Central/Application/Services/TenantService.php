<?php

namespace App\Modules\Central\Application\Services;

use Illuminate\Support\Str;
use App\Shared\Application\Services\BaseService;
use App\Modules\Central\Domain\Repositories\TenantRepositoryInterface;

class TenantService extends BaseService
{
    public function __construct(
        private TenantRepositoryInterface $repo
    ) {}

    public function create(array $data)
    {
        $data['uuid'] = (string) Str::uuid();
        $data['code'] = strtoupper(Str::random(8));
        $data['slug'] = Str::slug($data['name']);

        return $this->repo->create($data);
    }
}
