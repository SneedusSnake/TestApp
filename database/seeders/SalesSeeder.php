<?php

namespace Database\Seeders;

use App\Models\Sale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SalesSeeder extends Seeder
{
    use WithoutModelEvents;
    private const NUMBER_OF_SALES_TO_SEED = 1000000;
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
        $lastId = DB::scalar('SELECT MAX(id) FROM clients');

        for ($i = 0; $i < self::NUMBER_OF_SALES_TO_SEED/self::CHUNK_SIZE; $i++) {
            $sales = [];
            for ($j = 0; $j < self::CHUNK_SIZE; $j++) {
                $sales[] = Sale::factory()->make([
                    'client_id' =>rand(1, $lastId),
                    'created_at' => Carbon::today()->subDays(rand(0, 180))->subSeconds(rand(0, 86400))
                ])->toArray();
            }
            $this->insertSales($sales);
        }
    }

    private function insertSales(array $sales): void
    {
        $placeholders = join(',', array_fill(0, count($sales), '(?, ?, ?)'));
        $bindings = Arr::flatten($sales);

        DB::insert(
            "INSERT INTO sales(amount, client_id, created_at) VALUES $placeholders",
            $bindings
        );
    }
}
