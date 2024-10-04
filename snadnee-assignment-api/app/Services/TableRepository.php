<?php

namespace App\Services;

use App\Enums\Reservation\ReservationStatusEnum;
use App\Models\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TableRepository
{
    public function findOverlappingReservations(Table $table, string $reservationStartTime, string $reservationEndTime): Collection
    {
        return DB::table(DB::raw('(SELECT *, DATE_ADD(reservation_date_time, INTERVAL reservation_length_in_minutes MINUTE) as finish_time FROM reservations) as subquery'))
            ->where('subquery.table_id', '=', $table->id)
            ->where('subquery.status', '=', ReservationStatusEnum::ACTIVE->value)
            ->where(function ($query) use ($reservationStartTime, $reservationEndTime) {
                $query->where('subquery.reservation_date_time', '<=', $reservationEndTime)
                    ->orWhere('subquery.finish_time', '>=', $reservationStartTime);
            })
            ->get();
    }
}
