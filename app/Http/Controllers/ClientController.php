<?php

namespace App\Http\Controllers;

use App\Commands\CreateClientCommand;
use App\Handlers\CreateClientHandler;
use App\Http\Requests\CreateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ClientController extends Controller
{
    public function __construct(private CreateClientHandler $clientHandler)
    {

    }

    public function list(Request $request): JsonResponse
    {
        $clients = Client::query()
            ->when($request->input('ids'), function (Builder $query) use ($request) {
                $query->whereIn('id', $request->input('ids'));
            })
            ->with('emails')
            ->paginate(config('pagination.records_per_page'));

        return response()->json(
            ['data' => ClientResource::collection($clients)],
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
