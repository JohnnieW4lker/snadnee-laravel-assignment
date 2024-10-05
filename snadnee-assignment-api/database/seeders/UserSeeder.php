<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'first_name' => 'Jan',
            'last_name' => 'Novak',
            'email' => 'jannovak@example.com',
            'password' => Hash::make('Password12345'),
            'phone' => '+420111222333',
        ]);

        DB::table('users')->insert([
            'first_name' => 'Helena',
            'last_name' => 'Doskočilova',
            'email' => 'helena123@example.com',
            'password' => Hash::make('Password12345'),
            'phone' => '+420444555666',
        ]);

        /** @var User $janNovak */
        $janNovak = User::query()->where('email', '=', 'jannovak@example.com')->first();
        $janNovak->createToken('access');
    }
}
