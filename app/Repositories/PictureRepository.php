<?php

namespace App\Repositories;

use App\Models\Picture;
use Illuminate\Database\Eloquent\Collection;

class PictureRepository
{
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

    public function create(array $data): Picture
    {
        return Picture::create($data);
    }

    public function delete(int $id): bool
    {
        return Picture::findOrFail($id)->delete();
    }

    public function deleteByEntity(string $entityType, int $entityId): bool
    {
        return Picture::where('entity_type', $entityType)->where('entity_id', $entityId)->delete() > 0;
    }
    
    public function findById(int $id): ?Picture
    {
        return Picture::findOrFail($id);
    }
}
