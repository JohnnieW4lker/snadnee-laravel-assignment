<?php

namespace Database\Seeders;

use App\Enums\Reservation\ReservationStatusEnum;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $janNovak = User::query()
            ->where('first_name', '=', 'Jan')
            ->where('last_name', '=', 'Novak')
            ->first();

        $tableOne = Table::query()
            ->where('number', '=', 1)
            ->first();

        $tableSix = Table::query()
            ->where('number', '=', 6)
            ->first();

        $firstReservationTime = Carbon::now()->addDay()->setSecond(0)->setHour(18)->setMinute(30);
        $secondReservationTime = Carbon::now()->addDays(2)->setSecond(0)->setHour(18)->setMinute(30);

        DB::table('reservations')->insert([
            'reservation_date_time' => $firstReservationTime,
            'reservation_length_in_minutes' => 60,
            'people_count' => 2,
            'guest_first_name' => 'Jan',
            'guest_last_name' => 'Novak',
            'status' => ReservationStatusEnum::ACTIVE,
            'user_id' => $janNovak->id,
            'table_id' => $tableOne->id,
        ]);

        DB::table('reservations')->insert([
            'reservation_date_time' => $firstReservationTime,
            'reservation_length_in_minutes' => 30,
            'people_count' => 2,
            'guest_first_name' => 'Jan',
            'guest_last_name' => 'Novak',
            'status' => ReservationStatusEnum::CANCELLED,
            'user_id' => $janNovak->id,
            'table_id' => $tableOne->id,
        ]);

        DB::table('reservations')->insert([
            'reservation_date_time' => $secondReservationTime,
            'reservation_length_in_minutes' => 60,
            'people_count' => 4,
            'guest_first_name' => 'Jan',
            'guest_last_name' => 'Novak',
            'status' => ReservationStatusEnum::ACTIVE,
            'user_id' => $janNovak->id,
            'table_id' => $tableSix->id,
        ]);
    }
}
