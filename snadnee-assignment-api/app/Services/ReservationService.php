<?php

namespace App\Services;

use App\Enums\Reservation\ReservationStatusEnum;
use App\Http\Requests\Reservation\ReservationCreateRequest;
use App\Http\Resources\Reservation\ReservationResource;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReservationService
{

    public function getReservationsForCurrentUser(): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = auth()->user();

        return ReservationResource::collection($user->reservations());
    }

    public function createNewReservation(ReservationCreateRequest $reservationCreateRequest): ReservationResource
    {
        /** @var Table $table */
        $table = Table::find($reservationCreateRequest->validated('tableId'));

        $reservation = Reservation::create([
            'reservationDateTime' => $reservationCreateRequest->validated('reservationDateTime'),
            'peopleCount' => $reservationCreateRequest->validated('peopleCount'),
            'reservationLengthInMinutes' => $reservationCreateRequest->validated('reservationLengthInMinutes'),
            'guestFirstName' => $reservationCreateRequest->validated('guestFirstName'),
            'guestLastName' => $reservationCreateRequest->validated('guestLastName'),
            'table' => $table,
            'status' => ReservationStatusEnum::ACTIVE,
        ]);

        return ReservationResource::make($reservation);
    }

    public function cancelReservation(Reservation $reservation): void
    {
        $reservation->status = ReservationStatusEnum::CANCELLED;
        $reservation->save();
    }
}
