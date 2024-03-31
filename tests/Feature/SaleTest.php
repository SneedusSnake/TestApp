<?php

namespace Tests\Feature;

use App\Http\Resources\SaleResource;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_sales_resource_empty(): void
    {
        $response = $this->json('GET', '/api/sales');

        $response->assertStatus(200)->assertJson(['data' => []]);
    }

    public function test_sales_resource_returns_sales(): void
    {
        $sales = Sale::factory()->count(5)->create();
        $resource = SaleResource::collection($sales);
        $request = Request::create('/api/sales');

        $response = $this->json($request->method(), $request->url());

        $response->assertStatus(200)
            ->assertJson($resource->response($request)->getData(true));
    }

    public function test_sales_resource_pagination()
    {
        $sales = Sale::factory()->count(10)->create();
        $sales = $sales->slice(5);
        $resource = SaleResource::collection($sales);
        $request = Request::create('/api/sales', 'GET', ['page' => 2]);

        $response = $this->json($request->getMethod(), $request->getRequestUri());

        $response->assertStatus(200)
            ->assertJson($resource->response($request)->getData(true));
    }
}
