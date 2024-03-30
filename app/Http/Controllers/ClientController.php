<?php

namespace App\Http\Controllers;

use App\Rules\Domain;
use App\Util\DomainValidator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Client;
use App\Http\Resources\ClientResource;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    use ValidatesRequests;

    public function __construct(private DomainValidator $domainValidator)
    {

    }

    public function list(): JsonResponse
    {
        return response()->json(
            ['data' => ClientResource::collection(Client::query()->paginate(5))],
        );
    }

    /**
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|unique:clients',
            'website'    => ['required', 'unique:clients', 'url', new Domain($this->domainValidator)],
            'country_id' => 'required|exists:countries,id',
        ]);
        $client = new Client();
        $client->setRawAttributes(
            $request->all(['first_name', 'last_name', 'email', 'website', 'country_id'])
        );
        $client->save();

        return response()->json(['data' => new ClientResource($client)]);
    }
}
