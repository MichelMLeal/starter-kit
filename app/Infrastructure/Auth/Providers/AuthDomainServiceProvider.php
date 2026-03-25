<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\Providers;

use App\Domain\Auth\Actions\LoginAction;
use App\Domain\Auth\Actions\LogoutAction;
use App\Domain\Auth\Actions\RefreshTokenAction;
use App\Domain\Auth\Actions\RegisterAction;
use App\Domain\Auth\Repositories\RefreshTokenRepositoryInterface;
use App\Domain\Auth\Repositories\UserRepositoryInterface;
use App\Infrastructure\Auth\Repositories\EloquentRefreshTokenRepository;
use App\Infrastructure\Auth\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

final class AuthDomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(RefreshTokenRepositoryInterface::class, EloquentRefreshTokenRepository::class);

        $this->app->singleton(RegisterAction::class);
        $this->app->singleton(LoginAction::class);
        $this->app->singleton(RefreshTokenAction::class);
        $this->app->singleton(LogoutAction::class);
    }
}
