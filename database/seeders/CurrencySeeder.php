<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('currency')->insert([
            [
                'full_name'     => 'United States dollar',
                'name'          => 'USD',
                'exchange_rate' => 1.000000,
                'base_currency' => 1,
                'status'        => 1,
            ],
            [
                'full_name'     => 'Euro',
                'name'          => 'EUR',
                'exchange_rate' => 0.850000,
                'base_currency' => 0,
                'status'        => 1,
            ],
            [
                'full_name'     => 'Indian rupee',
                'name'          => 'INR',
                'exchange_rate' => 74.500000,
                'base_currency' => 0,
                'status'        => 1,
            ],
        ]);
    }
}
