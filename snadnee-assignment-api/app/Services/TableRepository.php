<?php

namespace App\Services;

use App\Enums\Reservation\ReservationStatusEnum;
use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class TableRepository
{
    public function findOverlappingReservations(Table $table, CarbonInterface $reservationStartTime, CarbonInterface $reservationEndTime): Collection
    {
        $now = Carbon::now()->setHour(0)->setMinute(0)->setSecond(0)->toAtomString();
        $reservations = Reservation::query()
            ->where('table_id', '=', $table->id)
            ->where('status', '=', ReservationStatusEnum::ACTIVE)
            ->where('reservation_date_time', '>=', $now)
            ->get();

        return $reservations->filter(function (Reservation $reservation) use ($reservationStartTime, $reservationEndTime) {
            $filteredReservationStartTime = Carbon::make($reservation->reservation_date_time);
            $filteredReservationEndTime = Carbon::make($reservation->reservation_date_time)->addMinutes($reservation->reservation_length_in_minutes);

            return $reservationStartTime < $filteredReservationEndTime && $reservationEndTime > $filteredReservationStartTime;
        });
    }
}
