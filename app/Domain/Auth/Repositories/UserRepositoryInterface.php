<?php

declare(strict_types=1);

namespace App\Domain\Auth\Repositories;

use App\Domain\Shared\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail(string $email): ?Model;
}
