<?php

declare(strict_types=1);

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Exceptions\TokenExpiredException;
use App\Domain\Auth\Repositories\RefreshTokenRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class RefreshTokenAction
{
    private const TOKEN_NAME = 'auth-token';

    public function __construct(
        private readonly RefreshTokenRepositoryInterface $refreshTokenRepository,
    ) {}

    /**
     * @return array{access_token: string, refresh_token: string}
     */
    public function execute(string $refreshToken): array
    {
        $storedToken = $this->refreshTokenRepository->findByToken($refreshToken);

        if (! $storedToken || $storedToken->isExpired()) {
            throw new TokenExpiredException;
        }

        $user = $storedToken->user;

        if (! $user) {
            $this->refreshTokenRepository->revokeAllForUser($storedToken->user_id);
            throw new TokenExpiredException;
        }

        $plainRefreshToken = Str::random(64);
        $hashedToken = hash('sha256', $plainRefreshToken);
        $expiresAt = now()->addDays(7);

        return DB::transaction(function () use ($user, $plainRefreshToken, $hashedToken, $expiresAt) {
            $this->refreshTokenRepository->revokeAllForUser($user->id);

            $accessToken = $user->createToken(self::TOKEN_NAME)->plainTextToken;

            $this->refreshTokenRepository->createForUser(
                userId: $user->id,
                token: $hashedToken,
                expiresAt: $expiresAt,
            );

            return [
                'access_token' => $accessToken,
                'refresh_token' => $plainRefreshToken,
            ];
        });
    }
}
