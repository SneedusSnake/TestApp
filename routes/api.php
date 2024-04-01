<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\StatisticsController;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/clients/', [ClientController::class, 'list']);

Route::post('/clients/', [ClientController::class, 'create']);

Route::get('/sales/', function (Request $request) {
   return response()->json(['data' => SaleResource::collection(Sale::query()->paginate(5))]);
});


Route::get('/statistics/sales', [StatisticsController::class, 'sales']);


