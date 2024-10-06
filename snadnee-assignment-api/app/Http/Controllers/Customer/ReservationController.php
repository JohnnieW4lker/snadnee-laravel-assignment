<?php

namespace App\Http\Controllers\Customer;

use App\Http\Middleware\AllowAuthenticatedOnly;
use App\Http\Requests\Reservation\ReservationCreateRequest;
use App\Http\Resources\Reservation\ReservationResource;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ReservationController extends Controller
{
    public function __construct(
        private readonly ReservationService $reservationService
    )
    {
        $this->middleware(AllowAuthenticatedOnly::class);
        $this->middleware('auth:sanctum');
    }

    public function listReservations(Request $request): AnonymousResourceCollection
    {
        return $this->reservationService->getReservationsForCurrentUser();
    }

    public function makeReservation(ReservationCreateRequest $reservationCreateRequest): ReservationResource
    {
        return $this->reservationService->createNewReservation($reservationCreateRequest);
    }

    public function cancelReservations(Reservation $reservation): JsonResponse
    {
        Gate::authorize('cancel', $reservation);
        $this->reservationService->cancelReservation($reservation);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
