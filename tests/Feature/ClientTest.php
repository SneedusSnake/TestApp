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

    public function test_clients_resource_filter_by_ids(): void
    {
        $clients = Client::factory()->count(5)->create();
        $request = Request::create('/api/clients', 'GET', [
            'ids' => [$clients->first()->id, $clients->last()->id]
        ]);

        $response = $this->json($request->getMethod(), $request->getRequestUri());

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $clients->first()->id])
            ->assertJsonFragment(['id' => $clients->last()->id])
            ->assertJsonMissing(['id' => $clients[1]->id]);
    }

    public function test_clients_resource_creates_client(): void
    {
        $data = [
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,
            'country_id' => Country::query()->first()->id,
            'email'      => fake()->unique()->email,
            'websites'    => [fake()->unique()->url, fake()->unique()->url],
        ];

        $response = $this->json('POST', '/api/clients', $data);

        $this->assertDatabaseHas('clients', [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'country_id' => $data['country_id'],
        ]);
        $response->assertStatus(200)->assertJsonFragment($data);
    }

    public function test_clients_resource_creates_client_emails(): void
    {
        $data = [
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,
            'country_id' => Country::query()->first()->id,
            'email'      => 'testMainEmail@gmail.com',
            'emails'     => ['testAdditionalEmail1@gmail.com', 'testAdditionalEmail2@gmail.com'],
            'websites'   => [fake()->unique()->url, fake()->unique()->url],
        ];

        $this->json('POST', '/api/clients', $data);

        $this->assertDatabaseHas('client_emails', [
            'email' => $data['email'],
            'is_main' => 1,
        ]);
        $this->assertDatabaseHas('client_emails', [
            'email' => $data['emails'][0],
            'is_main' => 0,
        ]);
        $this->assertDatabaseHas('client_emails', [
            'email' => $data['emails'][1],
            'is_main' => 0,
        ]);
    }


    public function test_clients_resource_creates_client_websites(): void
    {
        $data = [
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,
            'country_id' => Country::query()->first()->id,
            'email'      => fake()->unique()->email,
            'websites'    => ['http://website1.com', 'http://website2.com'],
        ];

        $this->json('POST', '/api/clients', $data);

        $this->assertDatabaseHas('client_websites', [
            'website' => $data['websites'][0],
        ]);
        $this->assertDatabaseHas('client_websites', [
            'website' => $data['websites'][1],
        ]);
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
                'websites',
            ]);
    }

    public function test_clients_resource_validates_unique_fields(): void
    {
        $existingClient = Client::factory()->create();

        $response = $this->json('POST', '/api/clients', [
            ...$existingClient->toArray(),
            'email' => $existingClient->emails->first()->email,
            'websites' => $existingClient->websites()->pluck('website'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
                'websites.0',
            ]);
    }

    public function test_clients_resource_validates_country_exists(): void
    {
        $data = [
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,
            'country_id' => 123456,
            'email'      => fake()->email,
            'websites'    => [fake()->url],
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
            'websites'   => ['not an url'],
        ];

        $response = $this->json('POST', '/api/clients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['websites.0']);
    }

    public function test_clients_resource_validates_website_domain(): void
    {
        $data = [
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,
            'country_id' => Country::query()->first()->id,
            'email'      => fake()->email,
            'websites'   => ['http://thisdomaindoesnotexist.com'],
        ];
        $this->domainValidator->invalid();

        $response = $this->json('POST', '/api/clients', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['websites.0']);
    }
}
