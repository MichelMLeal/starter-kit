<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\Repositories;

use App\Domain\Auth\Models\User;
use App\Domain\Auth\Repositories\UserRepositoryInterface;
use App\Infrastructure\Shared\Repositories\EloquentBaseRepository;

final class EloquentUserRepository extends EloquentBaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->newQuery()->where('email', $email)->first();
    }
}
