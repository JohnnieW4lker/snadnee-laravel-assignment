<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tables')->insert([
            'number' => 1,
            'seats' => 2,
        ]);

        DB::table('tables')->insert([
            'number' => 2,
            'seats' => 3,
        ]);

        DB::table('tables')->insert([
            'number' => 3,
            'seats' => 3,
        ]);

        DB::table('tables')->insert([
            'number' => 4,
            'seats' => 2,
        ]);

        DB::table('tables')->insert([
            'number' => 5,
            'seats' => 4,
        ]);

        DB::table('tables')->insert([
            'number' => 6,
            'seats' => 4,
        ]);
    }
}
