<?php

declare(strict_types=1);

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\DTOs\RegisterDTO;
use App\Domain\Auth\Models\User;
use App\Domain\Auth\Repositories\UserRepositoryInterface;

final class RegisterAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(RegisterDTO $dto): User
    {
        /** @var User */
        return $this->userRepository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password, // hashed by model cast
        ]);
    }
}
