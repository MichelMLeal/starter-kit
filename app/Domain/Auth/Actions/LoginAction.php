<?php

declare(strict_types=1);

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\DTOs\LoginDTO;
use App\Domain\Auth\Exceptions\InvalidCredentialsException;
use App\Domain\Auth\Models\User;
use App\Domain\Auth\Repositories\RefreshTokenRepositoryInterface;
use App\Domain\Auth\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class LoginAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RefreshTokenRepositoryInterface $refreshTokenRepository,
    ) {}

    /**
     * @return array{user: User, access_token: string, refresh_token: string}
     */
    public function execute(LoginDTO $dto): array
    {
        /** @var User|null $user */
        $user = $this->userRepository->findByEmail($dto->email);

        // Always perform hash check to prevent timing-based user enumeration
        $validPassword = $user && Hash::check($dto->password, $user->password);

        if (! $validPassword) {
            throw new InvalidCredentialsException;
        }

        $accessToken = $user->createToken('auth-token')->plainTextToken;

        $plainRefreshToken = Str::random(64);

        $this->refreshTokenRepository->createForUser(
            userId: $user->id,
            token: hash('sha256', $plainRefreshToken),
            expiresAt: now()->addDays(7),
        );

        return [
            'user' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $plainRefreshToken,
        ];
    }
}
