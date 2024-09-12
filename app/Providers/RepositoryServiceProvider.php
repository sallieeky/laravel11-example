<?php

namespace App\Providers;

use App\Interfaces\RoleAndPermissionRepository;
use App\Interfaces\UserRepository;
use App\Repositories\RoleAndPermissionRepositoryImpl;
use App\Repositories\UserRepositoryImpl;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class,UserRepositoryImpl::class);
        $this->app->bind(RoleAndPermissionRepository::class,RoleAndPermissionRepositoryImpl::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
