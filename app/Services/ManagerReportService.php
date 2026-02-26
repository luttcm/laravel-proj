<?php

namespace App\Services;

use App\Repositories\ReportRepository;
use App\Repositories\CalculationRepository;
use App\Services\Calculation\CalculationService;
use App\Services\Calculation\DTO\CalculationRequestDTO;
use App\Models\Calculation;
use App\Models\DraftsReports;
use App\Models\Reports;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ManagerReportService
{
    /** @var ReportRepository */
    protected $reportRepository;
    /** @var CalculationRepository */
    protected $calculationRepository;
    /** @var CalculationService */
    protected $calculationService;

    public function __construct(
        ReportRepository $reportRepository,
        CalculationRepository $calculationRepository,
        CalculationService $calculationService
    ) {
        $this->reportRepository = $reportRepository;
        $this->calculationRepository = $calculationRepository;
        $this->calculationService = $calculationService;
    }

    /**
     * Get reports for a manager.
     * @param class-string<Model> $modelClass
     * @param int $managerId
     * @return Collection<int, Model>
     */
    public function getReportsForManager(string $modelClass, int $managerId): Collection
    {
        return $this->reportRepository->getForManager($modelClass, $managerId);
    }

    /**
     * Get latest reports for a user.
     * @param int $userId
     * @param int $limit
     * @return Collection<int, Model>
     */
    public function getLatestReports(int $userId, int $limit = 10): Collection
    {
        return $this->reportRepository->getLatest(Reports::class, $userId, $limit);
    }

    /**
     * Save a report (draft or history).
     * @param array<string, mixed> $data
     * @param class-string<Model> $modelClass
     * @param int $managerId
     * @param bool $isHistory
     * @param int|null $existingCalculationId
     * @return Model
     */
    public function saveReport(array $data, string $modelClass, int $managerId, bool $isHistory = false, $existingCalculationId = null): Model
    {
        if ($existingCalculationId) {
            $calculation = $this->calculationRepository->find($existingCalculationId);
            if ($calculation) {
                $calculation->update($data);
                $calculationId = $calculation->id;
            } else {
                $calculationId = $this->saveCalculation($data, $managerId, $isHistory);
            }
        } else {
            $calculationId = $this->saveCalculation($data, $managerId, $isHistory);
        }

        $dto = CalculationRequestDTO::fromArray($data);
        $result = $this->calculationService->calculate($dto);

        $this->calculationRepository->update([
            'nds_id' => $data['nds_id'] ?? null,
            'spk_id' => $data['spk_id'] ?? null,
            'manager_payment' => $result->managerPayment,
            'manager_salary_brutto' => $result->managerSalaryBrutto,
            'per_unit_payment' => $result->perUnitPayment,
            'in_the_deal' => $result->inTheDeal,
        ], $calculationId);

        $reportData = [
            'manager_id' => $managerId,
            'date' => now()->toDateString(),
            'name' => auth()->user()->name ?? 'Без имени',
            'report_title' => $data['report_name'] ?? 'Отчет ' . now()->format('d.m.Y H:i'),
            'amount' => $result->managerPayment,
            'calculate_id' => $calculationId,
        ];

        if (isset($data['report_id']) && !empty($data['report_id'])) {
            $report = $this->reportRepository->findForManager($modelClass, $data['report_id'], $managerId);
            if ($report) {
                $report->update([
                    'date' => $reportData['date'],
                    'report_title' => $reportData['report_title'],
                    'amount' => $reportData['amount'],
                ]);
                return $report;
            }
        }

        return $this->reportRepository->create($modelClass, $reportData);
    }

    /**
     * Save calculation logic.
     * @param array<string, mixed> $data
     * @param int $managerId
     * @param bool $isHistory
     * @return int
     */
    private function saveCalculation(array $data, int $managerId, bool $isHistory = false): int
    {
        $numericFields = [
            'purchase_price', 'purchase_sum', 'markup_percent', 'selling_price',
            'selling_sum', 'prf_percent', 'deal_payment', 'per_unit_payment',
            'in_the_hand', 'manager_payment', 'manager_salary_brutto', 'in_the_deal',
            'spk_id'
        ];

        foreach ($numericFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = round((float)$data[$field], 2);
            }
        }

        $calculation = $this->calculationRepository->create(array_merge($data, ['user_id' => $managerId]));

        return $calculation->id;
    }

    /**
     * Get report with calculation details.
     * @param class-string<Model> $modelClass
     * @param int $id
     * @param int $managerId
     * @return array<string, mixed>
     */
    public function getReportWithCalculation(string $modelClass, int $id, int $managerId): array
    {
        /** @var \App\Models\Reports|\App\Models\DraftsReports|null $report */
        $report = $this->reportRepository->findForManager($modelClass, $id, $managerId);
        if (!$report) {
            return [];
        }

        $calculation = $this->calculationRepository->find($report->calculate_id);

        return [
            'report' => $report,
            'calculation' => $calculation,
        ];
    }
}
