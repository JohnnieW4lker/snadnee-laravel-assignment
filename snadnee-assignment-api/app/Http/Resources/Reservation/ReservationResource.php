<?php

namespace App\Http\Resources\Reservation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public static $wrap = '';

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'reservationDateTime' => $this->reservationDateTime,
            'peopleCount' => $this->peopleCount,
            'guestFirstName' => $this->guestFirstName,
            'guestLastName' => $this->guestLastName,
            'status' => $this->status,
        ];
    }
}
