<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Repositories;

use App\Domain\Shared\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class EloquentBaseRepository implements RepositoryInterface
{
    public function __construct(
        protected Model $model,
    ) {}

    public function findById(int|string $id): ?Model
    {
        return $this->model->newQuery()->find($id);
    }

    public function findOrFail(int|string $id): Model
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    public function all(): Collection
    {
        return $this->model->newQuery()->get();
    }

    public function create(array $data): Model
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(int|string $id, array $data): Model
    {
        $record = $this->findOrFail($id);
        $record->update($data);

        return $record->refresh();
    }

    public function delete(int|string $id): bool
    {
        return (bool) $this->findOrFail($id)->delete();
    }
}
