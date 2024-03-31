<?php

namespace App\Handlers;

use App\Commands\CreateClientCommand;
use App\Models\Client;
use App\Models\ClientEmail;
use App\Models\ClientWebsite;
use Illuminate\Database\Connection;

class CreateClientHandler
{
    public function __construct(private Connection $db)
    {

    }

    public function handle(CreateClientCommand $command): Client
    {
        return $this->createClient($command);
    }

    /**
     * @throws \Throwable
     */
    private function createClient(CreateClientCommand $command): Client
    {
        $this->db->beginTransaction();

        $client = new Client();
        $client->first_name = $command->getFirstName();
        $client->last_name = $command->getLastName();
        $client->country_id = $command->getCountryId();
        $client->save();

        $this->createClientEmails($client, $command->getEmail(), $command->getEmails());
        $this->createClientWebsites($client, $command->getWebsites());

        $this->db->commit();

        return $client;
    }

    private function createClientEmails(Client $client, string $mainEmail, array $additionalEmails = []): void
    {
        $client->emails()->saveMany([
                ...array_map(function (string $email) {
                    return new ClientEmail(['email' => $email]);
                }, $additionalEmails),
                new ClientEmail(['email' => $mainEmail, 'is_main' => 1])]
        );
    }

    private function createClientWebsites(Client $client, array $websites): void
    {
        $client->websites()->saveMany(array_map(function (string $website) {
            return new ClientWebsite(['website' => $website]);
        }, $websites));
    }
}
