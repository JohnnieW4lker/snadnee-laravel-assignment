<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TableSeeder::class,
            UserSeeder::class,
            ReservationSeeder::class,
        ]);
    }
}
