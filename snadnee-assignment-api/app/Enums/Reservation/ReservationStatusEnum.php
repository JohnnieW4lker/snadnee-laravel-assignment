<?php

namespace App\Enums\Reservation;

enum ReservationStatusEnum: string
{
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';
}
