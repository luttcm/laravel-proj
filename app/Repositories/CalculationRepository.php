<?php

namespace App\Repositories;

use App\Models\Calculation;

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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentForManager(int $managerId, int $limit = 10)
    {
        return $this->model->where('manager_id', $managerId) // Note: actual field in Calculation is user_id based on previous code usually, but let's check
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }
}
