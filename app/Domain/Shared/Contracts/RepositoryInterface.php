<?php

declare(strict_types=1);

namespace App\Domain\Shared\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function findById(int|string $id): ?Model;

    public function findOrFail(int|string $id): Model;

    public function all(): Collection;

    public function create(array $data): Model;

    public function update(int|string $id, array $data): Model;

    public function delete(int|string $id): bool;
}
