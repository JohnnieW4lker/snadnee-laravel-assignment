<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'number',
        'seats',
    ];

    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'seats' => 'integer',
        ];
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
