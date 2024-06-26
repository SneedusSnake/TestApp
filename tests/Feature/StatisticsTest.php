<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Sale;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_statistics_sales_route(): void
    {
        $totalSalesAmount = Sale::factory()->count(5)->create()->sum('amount');

        $response = $this->json('GET', '/api/statistics/sales');

        $response->assertStatus(200)->assertJsonFragment(['total' => $totalSalesAmount]);
    }

    public function test_statistics_sales_returns_aggregated_top_sales_for_clients(): void
    {
        $clients = Client::factory()->count(2)->create();
        $firstClientSales = Sale::factory()->for($clients[0])->count(5)->create();
        $secondClientSales = Sale::factory()->for($clients[1])->count(5)->create();

        $response = $this->json('GET', '/api/statistics/sales');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'clients' => [
                    [
                        'client_id' => $clients[0]->id,
                        'total_amount' => (string) $firstClientSales->sum('amount'),
                    ],
                    [
                        'client_id' => $clients[1]->id,
                        'total_amount' => (string) $secondClientSales->sum('amount'),
                    ],
                ],
            ]);
    }

    public function test_statistics_sales_filters_results_by_date(): void
    {
        $date = CarbonImmutable::today()->startOfDay();
        Sale::factory()->create(['created_at' => $date->subDay()]);
        $actualSale = Sale::factory()->create(['created_at' => $date]);

        $response = $this->json(
            'GET',
            '/api/statistics/sales',
            [
                'date_from' => $date,
                'date_to' => $date->endOfDay(),
            ]
        );

        $response->assertStatus(200)->assertJsonFragment([
            'total' => $actualSale->amount,
            'clients' => [
                [
                    'client_id' => $actualSale->client_id,
                    'total_amount' => (string)$actualSale->amount,
                ],
            ],
        ]);
    }

    public function test_statistics_clients_route(): void
    {
        $date = CarbonImmutable::today();
        Client::factory()->count(3)->sequence(
            ['created_at' => $date->subDay()],
            ['created_at' => $date->subDay()],
            ['created_at' => $date],
        )->create();

        $response = $this->json('GET', '/api/statistics/clients');

        $response->assertStatus(200)->assertJsonFragment([
                'date' => $date->subDay()->format('Y-m-d'),
                'count' => 2,
            ])->assertJsonFragment([
                'date' => $date->format('Y-m-d'),
                'count' => 1,
            ]);
    }

    public function test_statistics_clients_filters_by_date(): void
    {
        $date = CarbonImmutable::today();
        Client::factory()->count(3)->sequence(
            ['created_at' => $date->subDay()],
            ['created_at' => $date->subDay()],
            ['created_at' => $date],
        )->create();

        $response = $this->json('GET', '/api/statistics/clients', [
            'date_from' => $date->startOfDay(),
            'date_to' => $date->endOfDay(),
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            [
                'date' => $date->format('Y-m-d'),
                'count' => 1
            ],
        ])->assertJsonMissing([
            [
                'date' => $date->subDay()->format('Y-m-d'),
                'count' => 2,
            ]
        ]);
    }
}
