<?php

declare(strict_types=1);

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Exceptions\TokenExpiredException;
use App\Domain\Auth\Models\User;
use App\Domain\Auth\Repositories\RefreshTokenRepositoryInterface;
use Illuminate\Support\Str;

final class RefreshTokenAction
{
    public function __construct(
        private readonly RefreshTokenRepositoryInterface $refreshTokenRepository,
    ) {}

    /**
     * @return array{access_token: string, refresh_token: string}
     */
    public function execute(string $refreshToken): array
    {
        $storedToken = $this->refreshTokenRepository->findByToken($refreshToken);

        if (! $storedToken || $storedToken->expires_at->isPast()) {
            throw new TokenExpiredException;
        }

        /** @var User $user */
        $user = $storedToken->user;

        $this->refreshTokenRepository->revokeAllForUser($user->id);

        $accessToken = $user->createToken('auth-token')->plainTextToken;

        $plainRefreshToken = Str::random(64);

        $this->refreshTokenRepository->createForUser(
            userId: $user->id,
            token: hash('sha256', $plainRefreshToken),
            expiresAt: now()->addDays(7),
        );

        return [
            'access_token' => $accessToken,
            'refresh_token' => $plainRefreshToken,
        ];
    }
}
