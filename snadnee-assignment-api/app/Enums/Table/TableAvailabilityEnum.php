<?php

namespace App\Enums\Table;

enum TableAvailabilityEnum: string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
}
