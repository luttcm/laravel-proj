<?php

namespace Tests\Unit;

use App\Models\FinReport;
use App\Repositories\FinReportRepository;
use App\Services\Calculation\Strategies\FinDirectorCalculationStrategy;
use App\Services\FinReportService;
use Mockery;
use Tests\TestCase;

class FinReportServiceTest extends TestCase
{
    protected $service;
    protected $repository;
    protected $calculationStrategy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(FinReportRepository::class);
        $this->calculationStrategy = Mockery::mock(FinDirectorCalculationStrategy::class);
        $this->service = new FinReportService($this->repository, $this->calculationStrategy);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_createReport_calls_calculation_and_repository(): void
    {
        $data = [
            'report_title' => 'Test',
            'amount' => 1000,
        ];
        $userId = 1;

        $calcResult = new \App\Services\Calculation\DTO\FinDirectorCalculationResultDTO(
            remainder: 100,
            netSales: 900,
            paymentManager: 50,
            paymentSpk: 50,
            profit: 100,
            markup: 10
        );

        $this->calculationStrategy->shouldReceive('calculate')
            ->once()
            ->andReturn($calcResult);

        $this->repository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) use ($userId) {
                return $arg['user_id'] === $userId && $arg['report_title'] === 'Test';
            }))
            ->andReturn(new FinReport());

        $result = $this->service->createReport($data, $userId);

        $this->assertInstanceOf(FinReport::class, $result);
    }

    public function test_deleteReport_returns_false_if_not_found(): void
    {
        $id = 1;
        $userId = 1;

        $this->repository->shouldReceive('findForUser')
            ->once()
            ->with($id, $userId)
            ->andReturn(null);

        $result = $this->service->deleteReport($id, $userId);

        $this->assertFalse($result);
    }
}
