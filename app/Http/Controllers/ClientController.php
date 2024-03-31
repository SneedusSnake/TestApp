<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Models\Client;
use App\Http\Resources\ClientResource;
use Illuminate\Validation\ValidationException;

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
            $request->all(['first_name', 'last_name', 'email', 'website', 'country_id'])
        );
        $client->save();

        return response()->json(['data' => new ClientResource($client)]);
    }
}
