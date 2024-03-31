<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientRequest;
use App\Models\ClientEmail;
use App\Models\ClientWebsite;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Models\Client;
use App\Http\Resources\ClientResource;

class ClientController extends Controller
{
    public function list(): JsonResponse
    {
        return response()->json(
            ['data' => ClientResource::collection(Client::query()->paginate(5))],
        );
    }

    public function create(CreateClientRequest $request): JsonResponse
    {
        $client = new Client();
        $client->setRawAttributes(
            $request->all(['first_name', 'last_name','country_id'])
        );
        $client->save();
        $client->emails()->saveMany([
            ...array_map(function (string $email) {
                return new ClientEmail(['email' => $email]);
            }, $request->get('emails', [])),
            new ClientEmail(['email' => $request->get('email'), 'is_main' => 1])]
        );
        $client->websites()->saveMany(array_map(function (string $website) {
            return new ClientWebsite(['website' => $website]);
        }, $request->get('websites')));
        $client->loadMissing(['emails', 'websites']);

        return response()->json(['data' => new ClientResource($client)]);
    }
}
