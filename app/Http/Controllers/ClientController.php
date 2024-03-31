<?php

namespace App\Http\Controllers;

use App\Commands\CreateClientCommand;
use App\Handlers\CreateClientHandler;
use App\Http\Requests\CreateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ClientController extends Controller
{
    public function __construct(private CreateClientHandler $clientHandler)
    {

    }

    public function list(): JsonResponse
    {
        return response()->json(
            ['data' => ClientResource::collection(Client::query()->paginate(5))],
        );
    }

    public function create(CreateClientRequest $request): JsonResponse
    {
        $command = CreateClientCommand::fromArray($request->all());
        $client = $this->clientHandler->handle($command);
        $client->loadMissing(['emails', 'websites']);

        return response()->json(['data' => new ClientResource($client)]);
    }
}
