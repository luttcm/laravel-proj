<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Reports;
use App\Models\DraftsReports;
use App\Models\Calculation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagersTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'manager']);
        $this->actingAs($this->user);
    }

    public function test_can_view_calculation_page(): void
    {
        $response = $this->get(route('managers.calculation'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.managers.calculation');
    }

    public function test_can_calculate_deal(): void
    {
        $response = $this->post(route('managers.calculate'), [
            'selling_name' => 'ООО (ОСН)',
            'purchase_price' => 100,
            'selling_price' => 150,
            'quantity' => 10,
            'nds_id' => 1,
            'spk_id' => 1,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'calculations' => [
                'nacenka',
                'companyProfit',
                'totalTaxes',
            ]
        ]);
    }

    public function test_can_store_draft_report(): void
    {
        $response = $this->post(route('managers.store-drafts-report'), [
            'report_name' => 'Draft Report',
            'selling_name' => 'ООО (ОСН)',
            'purchase_price' => 100,
            'selling_price' => 150,
            'quantity' => 10,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('drafts_reports', [
            'report_title' => 'Draft Report',
            'manager_id' => $this->user->id,
        ]);
    }

    public function test_can_store_final_report(): void
    {
        $response = $this->post(route('managers.store-report'), [
            'report_name' => 'Final Report',
            'selling_name' => 'ООО (ОСН)',
            'purchase_price' => 100,
            'selling_price' => 150,
            'quantity' => 10,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reports', [
            'report_title' => 'Final Report',
            'manager_id' => $this->user->id,
        ]);
    }

    public function test_can_get_report_json(): void
    {
        $report = Reports::factory()->create(['manager_id' => $this->user->id]);

        $response = $this->get(route('managers.get-report', $report->id) . '?type=history');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
    }
}
