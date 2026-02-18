<?php

namespace App\Services;

use App\Repositories\FinReportRepository;
use App\Services\Calculation\DTO\FinDirectorCalculationRequestDTO;
use App\Services\Calculation\Strategies\FinDirectorCalculationStrategy;
use App\Models\FinReport;
use Illuminate\Pagination\LengthAwarePaginator;

class FinReportService
{
    protected $repository;
    protected $calculationStrategy;

    public function __construct(
        FinReportRepository $repository,
        FinDirectorCalculationStrategy $calculationStrategy
    ) {
        $this->repository = $repository;
        $this->calculationStrategy = $calculationStrategy;
    }

    /**
     * Get paginated reports for the current user.
     */
    public function getPaginatedForUser(int $userId): LengthAwarePaginator
    {
        return $this->repository->getPaginatedForUser($userId);
    }

    /**
     * Create a new financial report.
     */
    public function createReport(array $data, int $userId): FinReport
    {
        $data['user_id'] = $userId;
        
        $data['received_amount'] = $data['received_amount'] ?? 0;
        $data['date'] = $data['date'] ?? now()->toDateString();
        $data['bonus_client'] = $data['bonus_client'] ?? 0;
        $data['kickback'] = $data['kickback'] ?? 0;
        $data['net_sales'] = $data['net_sales'] ?? 0;
        $data['remainder'] = $data['remainder'] ?? 0;
        $data['supplier_amount'] = $data['supplier_amount'] ?? 0;
        $data['payment_manager'] = $data['payment_manager'] ?? 0;
        $data['payment_spk'] = $data['payment_spk'] ?? 0;
        $data['profit'] = $data['profit'] ?? 0;
        $data['markup'] = $data['markup'] ?? 0;
        $data['nds_percent'] = $data['nds_percent'] ?? 0;

        $calcRequest = FinDirectorCalculationRequestDTO::fromArray($data);
        $calcResult = $this->calculationStrategy->calculate($calcRequest);

        $data['remainder'] = $calcResult->remainder;
        $data['net_sales'] = $calcResult->netSales;
        $data['payment_manager'] = $calcResult->paymentManager;
        $data['payment_spk'] = $calcResult->paymentSpk;
        $data['profit'] = $calcResult->profit;
        $data['markup'] = $calcResult->markup;

        return $this->repository->create($data);
    }

    /**
     * Update an existing financial report.
     */
    public function updateReport(int $id, array $data, int $userId): bool
    {
        $report = $this->repository->findForUser($id, $userId);
        if (!$report) {
            return false;
        }

        $data['kickback'] = $data['kickback'] ?? 0;

        $calcRequest = FinDirectorCalculationRequestDTO::fromArray($data);
        $calcResult = $this->calculationStrategy->calculate($calcRequest);

        $data['remainder'] = $calcResult->remainder;
        $data['net_sales'] = $calcResult->netSales;
        $data['payment_manager'] = $calcResult->paymentManager;
        $data['payment_spk'] = $calcResult->paymentSpk;
        $data['profit'] = $calcResult->profit;
        $data['markup'] = $calcResult->markup;

        return $this->repository->update($data, $id);
    }

    /**
     * Find a report for a user.
     */
    public function findForUser(int $id, int $userId): ?FinReport
    {
        return $this->repository->findForUser($id, $userId);
    }

    /**
     * Delete a report for a user.
     */
    public function deleteReport(int $id, int $userId): bool
    {
        $report = $this->repository->findForUser($id, $userId);
        if (!$report) {
            return false;
        }

        return $this->repository->delete($id);
    }
}
