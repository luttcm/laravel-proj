<?php

namespace App\Repositories;

use App\Models\Spk;
use Illuminate\Database\Eloquent\Collection;

class SpkRepository
{
    /**
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Models\Spk>
     */
    public function getAllPaginated(int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Spk::paginate($perPage);
    }

    public function findById(int $id): ?Spk
    {
        /** @var Spk|null $spk */
        $spk = Spk::find($id);
        return $spk;
    }

    /**
     * @param array<string, mixed> $data
     * @return Spk
     */
    public function create(array $data): Spk
    {
        /** @var Spk $spk */
        $spk = Spk::create($data);
        return $spk;
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $spk = $this->findById($id);
        if (!$spk) {
            return false;
        }
        return $spk->update($data);
    }

    public function delete(int $id): bool
    {
        $spk = $this->findById($id);
        if (!$spk) {
            return false;
        }
        return (bool)$spk->delete();
    }
}
