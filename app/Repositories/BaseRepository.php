<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @template TModel of Model
 */
abstract class BaseRepository
{
    /**
     * @var TModel
     */
    protected $model;

    /**
     * BaseRepository constructor.
     * @param TModel $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all instances of the model.
     * @return Collection<int, TModel>
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Create a new record in the database.
     * @param array<string, mixed> $data
     * @return TModel
     */
    public function create(array $data): Model
    {
        /** @var TModel $result */
        $result = $this->model->create($data);
        return $result;
    }

    /**
     * Update the record in the database.
     * @param array<string, mixed> $data
     * @param int|string $id
     * @param string $attribute
     * @return bool
     */
    public function update(array $data, $id, $attribute = "id"): bool
    {
        return (bool) $this->model->where($attribute, $id)->update($data);
    }

    /**
     * Delete a record in the database.
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return (bool) $this->model->destroy($id);
    }

    /**
     * Find a record in the database.
     * @param int|string $id
     * @param array<int, string> $columns
     * @return TModel|null
     */
    public function find($id, $columns = array('*')): ?Model
    {
        /** @var TModel|null $result */
        $result = $this->model->find($id, $columns);
        return $result;
    }

    /**
     * Find a record by a specific attribute.
     * @param string $attribute
     * @param string $value
     * @param array<int, string> $columns
     * @return TModel|null
     */
    public function findBy(string $attribute, string $value, $columns = array('*')): ?Model
    {
        /** @var TModel|null $result */
        $result = $this->model->where($attribute, '=', $value)->first($columns);
        return $result;
    }
}
