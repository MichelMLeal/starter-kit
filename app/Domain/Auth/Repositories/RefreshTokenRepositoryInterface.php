<?php

declare(strict_types=1);

namespace App\Domain\Auth\Repositories;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

interface RefreshTokenRepositoryInterface
{
    public function createForUser(int $userId, string $token, DateTimeInterface $expiresAt): Model;

    public function findByToken(string $token): ?Model;

    public function revokeAllForUser(int $userId): void;

    public function deleteExpired(): int;
}
