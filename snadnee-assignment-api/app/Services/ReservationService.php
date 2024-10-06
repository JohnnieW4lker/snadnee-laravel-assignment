<?php

namespace App\Services;

use App\Enums\Reservation\ReservationStatusEnum;
use App\Http\Requests\Reservation\ReservationCreateRequest;
use App\Http\Resources\Reservation\ReservationResource;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class ReservationService
{

    public function getReservationsForCurrentUser(): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = auth()->user();
        $now = Carbon::now()->setHour(0)->setMinute(0)->setSecond(0)->toAtomString();

        return ReservationResource::collection(
            $user->reservations()
                ->where('status', '=', ReservationStatusEnum::ACTIVE)
                ->where('reservation_date_time', '>=', $now)->get()
        );
    }

    public function createNewReservation(ReservationCreateRequest $reservationCreateRequest): ReservationResource
    {
        $currentUser = $reservationCreateRequest->user();

        $reservation = Reservation::create([
            'reservation_date_time' => $reservationCreateRequest->validated('reservationDateTime'),
            'people_count' => $reservationCreateRequest->validated('peopleCount'),
            'reservation_length_in_minutes' => $reservationCreateRequest->validated('reservationLengthInMinutes'),
            'guest_first_name' => $reservationCreateRequest->validated('guestFirstName'),
            'guest_last_name' => $reservationCreateRequest->validated('guestLastName'),
            'table_id' => $reservationCreateRequest->validated('tableId'),
            'user_id' => $currentUser->id,
            'status' => ReservationStatusEnum::ACTIVE,
        ]);

        return ReservationResource::make($reservation);
    }

    public function cancelReservation(Reservation $reservation): void
    {
        if ($reservation->status === ReservationStatusEnum::CANCELLED) {
            abort(Response::HTTP_NOT_FOUND, 'Reservation not found.');
        }

        $reservation->status = ReservationStatusEnum::CANCELLED;
        $reservation->save();
    }
}
