<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\Repositories;

use App\Domain\Auth\Models\RefreshToken;
use App\Domain\Auth\Repositories\RefreshTokenRepositoryInterface;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

final class EloquentRefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(
        private readonly RefreshToken $model,
    ) {}

    public function createForUser(int $userId, string $token, DateTimeInterface $expiresAt): Model
    {
        return $this->model->newQuery()->create([
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findByToken(string $token): ?Model
    {
        return $this->model->newQuery()->where('token', hash('sha256', $token))->first();
    }

    public function revokeAllForUser(int $userId): void
    {
        $this->model->newQuery()->where('user_id', $userId)->delete();
    }

    public function deleteExpired(): int
    {
        return $this->model->newQuery()->where('expires_at', '<', now())->delete();
    }
}
