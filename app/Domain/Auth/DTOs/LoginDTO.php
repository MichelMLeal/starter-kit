<?php

declare(strict_types=1);

namespace App\Domain\Auth\DTOs;

use App\Domain\Shared\DTOs\BaseDTO;

final class LoginDTO extends BaseDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {}
}
