<?php

declare(strict_types=1);

namespace App\Domain\Auth\Repositories;

use App\Domain\Auth\Models\RefreshToken;
use DateTimeInterface;

interface RefreshTokenRepositoryInterface
{
    public function createForUser(int $userId, string $token, DateTimeInterface $expiresAt): RefreshToken;

    public function findByToken(string $token): ?RefreshToken;

    public function revokeAllForUser(int $userId): void;

    public function deleteExpired(): int;
}
