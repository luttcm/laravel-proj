<?php

namespace App\Repositories;

use App\Models\Picture;
use Illuminate\Database\Eloquent\Collection;

class PictureRepository
{
    /**
     * @param string $entityType
     * @param int $entityId
     * @param int|null $limit
     * @return Collection<int, Picture>
     */
    public function getByEntity(string $entityType, int $entityId, int $limit = null): Collection
    {
        $query = Picture::where('entity_type', $entityType)->where('entity_id', $entityId);
        
        if ($limit !== null) {
            $query->limit($limit);
        }
        
        return $query->get();
    }

    public function countByEntity(string $entityType, int $entityId): int
    {
        return Picture::where('entity_type', $entityType)->where('entity_id', $entityId)->count();
    }

    /**
     * @param array<string, mixed> $data
     * @return Picture
     */
    public function create(array $data): Picture
    {
        /** @var Picture $picture */
        $picture = Picture::create($data);
        return $picture;
    }

    public function delete(int $id): bool
    {
        $picture = $this->findById($id);
        if (!$picture) {
            return false;
        }
        return (bool)$picture->delete();
    }

    public function deleteByEntity(string $entityType, int $entityId): bool
    {
        return Picture::where('entity_type', $entityType)->where('entity_id', $entityId)->delete() > 0;
    }
    
    public function findById(int $id): ?Picture
    {
        /** @var Picture|null $picture */
        $picture = Picture::find($id);
        return $picture;
    }
}
