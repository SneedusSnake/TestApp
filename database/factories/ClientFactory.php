<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientEmail;
use App\Models\ClientWebsite;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $countries = Country::all();

        return [
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'country_id' => $countries->random()->id,
        ];
    }

    public function configure(): self
    {
        return $this->afterCreating(function (Client $client) {
            ClientEmail::factory()->for($client)->create();
            ClientWebsite::factory()->for($client)->create();
        });
    }
}
