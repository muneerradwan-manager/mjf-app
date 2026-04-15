<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Central\Domain\Repositories\TenantRepositoryInterface;
use App\Modules\Central\Infrastructure\Repositories\TenantRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TenantRepositoryInterface::class,
            TenantRepository::class
        );
    }

    public function boot(): void {}
}
