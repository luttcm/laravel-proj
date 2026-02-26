<?php

namespace App\Repositories;

use App\Models\Calculation;

/**
 * @extends BaseRepository<Calculation>
 */
class CalculationRepository extends BaseRepository
{
    public function __construct(Calculation $model)
    {
        parent::__construct($model);
    }

    /**
     * Get recent calculations for a manager.
     *
     * @param int $managerId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection<int, Calculation>
     */
    public function getRecentForManager(int $managerId, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->where('manager_id', $managerId)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }
}
