<?php

namespace App\Models;

use App\Enums\Reservation\ReservationStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'reservationDateTime',
        'reservationLengthInMinutes',
        'peopleCount',
        'guestFirstName',
        'guestLastName',
        'status,'
    ];

    protected function casts(): array
    {
        return [
            'reservationDateTime' => 'datetime',
            'peopleCount' => 'integer',
            'reservationLengthInMinutes' => 'integer',
        ];
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ReservationStatusEnum::from($value),
            set: fn (ReservationStatusEnum $value) => $value->value,
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
}
