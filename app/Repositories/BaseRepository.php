<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all instances of the model.
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Create a new record in the database.
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update the record in the database.
     * @param array $data
     * @param int|string $id
     * @param string $attribute
     * @return bool
     */
    public function update(array $data, $id, $attribute = "id"): bool
    {
        return $this->model->where($attribute, $id)->update($data);
    }

    /**
     * Delete a record in the database.
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return $this->model->destroy($id);
    }

    /**
     * Find a record in the database.
     * @param int|string $id
     * @param array $columns
     * @return Model|null
     */
    public function find($id, $columns = array('*')): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Find a record by a specific attribute.
     * @param string $attribute
     * @param string $value
     * @param array $columns
     * @return Model|null
     */
    public function findBy(string $attribute, string $value, $columns = array('*')): ?Model
    {
        return $this->model->where($attribute, '=', $value)->first($columns);
    }
}
