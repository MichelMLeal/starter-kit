<?php

declare(strict_types=1);

namespace App\Domain\Auth\Repositories;

use App\Domain\Auth\Models\User;
use App\Domain\Shared\Contracts\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?User;
}
