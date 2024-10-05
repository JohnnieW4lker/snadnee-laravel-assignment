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
            'reservationDateTime' => $this->reservation_date_time,
            'peopleCount' => $this->people_count,
            'guestFirstName' => $this->guest_first_name,
            'guestLastName' => $this->guest_last_name,
            'status' => $this->status,
        ];
    }
}
