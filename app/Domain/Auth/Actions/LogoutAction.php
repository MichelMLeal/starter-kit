<?php

declare(strict_types=1);

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Models\User;
use App\Domain\Auth\Repositories\RefreshTokenRepositoryInterface;

final class LogoutAction
{
    public function __construct(
        private readonly RefreshTokenRepositoryInterface $refreshTokenRepository,
    ) {}

    public function execute(User $user): void
    {
        if ($token = $user->currentAccessToken()) {
            $token->delete();
        }

        $this->refreshTokenRepository->revokeAllForUser($user->id);
    }
}
