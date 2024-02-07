<?php

namespace Src\User\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\User\Domain\Repositories\UserRepository;
use Src\User\Domain\Services\AuthService;
use Src\User\Infrastructure\Repositories\UserRepositoryInterface;
use Src\User\Infrastructure\Services\AuthServiceInterface;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(base_path('src/User/Infrastructure/Database/Migrations'));
        $this->loadRoutesFrom(base_path('src/User/Presentation/API/V1/api.php'));
    }
}
