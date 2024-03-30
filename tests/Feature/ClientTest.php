<?php

namespace Tests\Feature;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\Country;
use App\Util\DomainValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\Stubs\StubDomainValidator;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    private StubDomainValidator $domainValidator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->domainValidator = new StubDomainValidator();
        $this->app->bind(DomainValidator::class, function () {
            return $this->domainValidator;
        });
    }

    public function test_clients_resource_empty(): void
    {
        $response = $this->json('GET', '/api/clients');

        $response->assertStatus(200)->assertJson(['data' => []]);
    }

    public function test_clients_resource_returns_clients(): void
    {
        $clients = Client::factory()->count(5)->create();
        $resource = ClientResource::collection($clients);
        $request = Request::create('/api/clients');

        $response = $this->json($request->method(), $request->url());

        $response->assertStatus(200)
            ->assertJson($resource->response($request)->getData(true));
    }

    public function test_clients_resource_pagination(): void
    {
        $clients = Client::factory()->count(10)->create();
        $clients = $clients->slice(5);
        $resource = ClientResource::collection($clients);
        $request = Request::create('/api/clients', 'GET', ['page' => 2]);

        $response = $this->json($request->getMethod(), $request->getRequestUri());

        $response->assertStatus(200)
            ->assertJson($resource->response($request)->getData(true));
    }

    public function test_clients_resource_creates_client(): void
    {
        $data = [
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,
            'country_id' => Country::query()->first()->id,
            'email'      => fake()->email,
            'website'    => fake()->url,
        ];

        $response = $this->json('POST', '/api/clients', $data);

        $this->assertDatabaseHas('clients', $data);
        $response->assertStatus(200)->assertJsonFragment($data);
    }

    public function test_clients_resource_required_fields_validation(): void
    {
        $response = $this->json('POST', '/api/clients', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'country_id',
                'email',
                'website',
            ]);
    }

    public function test_clients_resource_validates_unique_fields(): void
    {
        $existingClient = Client::factory()->create([
            'email'      => fake()->email,
            'website'    => fake()->url,
        ]);

        $response = $this->json('POST', '/api/clients', $existingClient->toArray());

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
                'website',
            ]);
    }

    public function test_clients_resource_validates_country_exists(): void
    {
        $data = [
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,
            'country_id' => 123456,
            'email'      => fake()->email,
            'website'    => fake()->url,
        ];

        $response = $this->json('POST', '/api/clients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['country_id']);
    }

    public function test_clients_resource_validates_website_url(): void
    {
        $data = [
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,
            'country_id' => Country::query()->first()->id,
            'email'      => fake()->email,
            'website'    => 'not an url',
        ];

        $response = $this->json('POST', '/api/clients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['website']);
    }

    public function test_clients_resource_validates_website_domain(): void
    {
        $data = [
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,
            'country_id' => Country::query()->first()->id,
            'email'      => fake()->email,
            'website'    => 'http://thisdomaindoesnotexist.com',
        ];
        $this->domainValidator->invalid();

        $response = $this->json('POST', '/api/clients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['website']);
    }
}
