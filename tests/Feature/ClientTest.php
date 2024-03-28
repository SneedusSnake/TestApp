<?php

namespace Tests\Feature;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_clients_resource_empty(): void
    {
        $response = $this->get('/api/clients');

        $response->assertStatus(200)->assertJson(['data' => []]);
    }

    public function test_clients_resource_returns_clients(): void
    {
        $clients = Client::factory()->count(5)->create();
        $resource = ClientResource::collection($clients);
        $request = Request::create('/api/clients');

        $response = $this->get($request->url());

        $response->assertStatus(200)
            ->assertJson($resource->response($request)->getData(true));
    }

    public function test_clients_resource_pagination(): void
    {
        $clients = Client::factory()->count(10)->create();
        $clients = $clients->slice(5);
        $resource = ClientResource::collection($clients);
        $request = Request::create('/api/clients', 'GET', ['page' => 2]);

        $response = $this->get($request->getRequestUri());

        $response->assertStatus(200)
            ->assertJson($resource->response($request)->getData(true));
    }
}
