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

    public $timestamps = true;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'reservation_date_time',
        'reservation_length_in_minutes',
        'people_count',
        'guest_first_name',
        'guest_last_name',
        'status',
        'table_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'reservation_date_time' => 'datetime',
            'people_count' => 'integer',
            'reservation_length_in_minutes' => 'integer',
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
        return $this->belongsTo(User::class, 'id');
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'id');
    }
}
