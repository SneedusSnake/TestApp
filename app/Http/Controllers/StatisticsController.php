<?php

namespace App\Http\Controllers;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StatisticsController extends Controller
{
    public function __construct(private Connection $connection)
    {

    }

    public function sales(Request $request): JsonResponse
    {
        $filterByDates = function (Builder $query) use ($request) {
            $query->when($request->input('date_from'), function (Builder $query) use ($request) {
                $query->where('created_at', '>=', $request->input('date_from'));
            });

            $query->when($request->input('date_to'), function (Builder $query) use ($request) {
                $query->where('created_at', '<=', $request->input('date_to'));
            });
        };

        $total = (int) $this->connection->table('sales')->tap($filterByDates)
            ->sum('amount');

        $topSales = $this->connection->table('sales')
            ->selectRaw('client_id, SUM(amount) as total_amount')
            ->tap($filterByDates)
            ->groupBy('client_id')
            ->orderBy('total_amount', 'DESC')
            ->limit(9)
            ->get();

        return response()->json(['data' => [
            'clients' => $topSales,
            'total' => $total,
        ]]);
    }

    public function clients(Request $request): JsonResponse
    {
        $clients = $this->connection->table('clients')
            ->selectRaw('COUNT(id) as count, DATE(created_at) as date')
            ->when($request->input('date_from'), function (Builder $query) use ($request) {
                $query->where('created_at', '>=', $request->input('date_from'));
            })
            ->when($request->input('date_to'), function (Builder $query) use ($request) {
                $query->where('created_at', '<=', $request->input('date_to'));
            })
            ->groupBy('date')
            ->limit(config('pagination.records_per_page'))
            ->orderBy('date', 'desc')
            ->get();

        return response()->json(['data' => $clients]);
    }
}
