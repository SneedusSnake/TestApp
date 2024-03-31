<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientEmail;
use App\Models\ClientWebsite;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ClientsSeeder extends Seeder
{
    use WithoutModelEvents;
    private const NUMBER_OF_CLIENTS_TO_SEED = 500000;
    private const CHUNK_SIZE = 10000;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        ini_set('memory_limit', '512M');
        DB::disableQueryLog();

        for ($i = 0; $i < self::NUMBER_OF_CLIENTS_TO_SEED/self::CHUNK_SIZE; $i++) {
            $clients = [];
            $emails = [];
            $websites = [];
            for ($j = 0; $j < self::CHUNK_SIZE; $j++) {
                $clients[] = Client::factory()->make([
                    'created_at' => Carbon::today()
                        ->subDays(rand(0, 180))
                        ->subSeconds(rand(0, 86400))
                ])->toArray();
            }
            $this->insertClients($clients);

            for ($j = 0; $j < self::CHUNK_SIZE; $j++) {
                $clientId = $i*self::CHUNK_SIZE+$j+1;

                $emails[] = ClientEmail::factory()->make(['client_id' => $clientId])->toArray();
                $websites[] = ClientWebsite::factory()->make(['client_id' => $clientId])->toArray();
            }
            $this->insertEmails($emails);
            $this->insertWebsites($websites);
        }
    }

    private function insertClients(array $clients)
    {
        $placeholders = join(',', array_fill(0, count($clients), '(?, ?, ?, ?)'));
        $bindings = Arr::flatten($clients);
        DB::insert(
                "INSERT INTO clients(first_name, last_name, country_id, created_at) VALUES $placeholders",
                $bindings
            );
    }

    private function insertEmails(array $emails)
    {
        $placeholders = join(',', array_fill(0, count($emails), '(?, ?, ?)'));
        $bindings = Arr::flatten($emails);
        DB::insert(
            "INSERT INTO client_emails(client_id, email, is_main) VALUES $placeholders",
            $bindings
        );
    }

    private function insertWebsites(array $websites)
    {
        $placeholders = join(',', array_fill(0, count($websites), '(?, ?)'));
        $bindings = Arr::flatten($websites);
        DB::insert(
            "INSERT INTO client_websites(client_id, website) VALUES $placeholders",
            $bindings
        );
    }
}
