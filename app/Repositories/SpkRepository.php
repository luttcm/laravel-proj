<?php

namespace App\Repositories;

use App\Models\Spk;
use Illuminate\Database\Eloquent\Collection;

class SpkRepository
{
    public function getAllPaginated(int $perPage = 10)
    {
        return Spk::paginate($perPage);
    }

    public function findById(int $id): ?Spk
    {
        return Spk::findOrFail($id);
    }

    public function create(array $data): Spk
    {
        return Spk::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $spk = $this->findById($id);
        return $spk->update($data);
    }

    public function delete(int $id): bool
    {
        $spk = $this->findById($id);
        return $spk->delete();
    }
}
