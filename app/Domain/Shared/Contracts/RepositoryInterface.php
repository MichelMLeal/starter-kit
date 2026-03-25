<?php

declare(strict_types=1);

namespace App\Domain\Shared\Contracts;

interface RepositoryInterface
{
    public function findById(int|string $id): ?object;

    public function findOrFail(int|string $id): object;

    /** @return iterable<object> */
    public function all(): iterable;

    public function create(array $data): object;

    public function update(int|string $id, array $data): object;

    public function delete(int|string $id): bool;
}
