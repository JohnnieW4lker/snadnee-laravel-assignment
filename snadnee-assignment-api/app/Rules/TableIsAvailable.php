<?php

namespace App\Rules;

use App\Models\Table;
use App\Services\TableRepository;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TableIsAvailable implements ValidationRule
{
    public function __construct(
        protected string $reservationDateTime,
        protected string $reservationLengthInMinutes,
    )
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var Table $table */
        $table = Table::find($value);

        if ($table === null) {
            $fail('Table does not exist');
        }

        $reservationEndTime = Carbon::make($this->reservationDateTime)
            ->addMinutes(intval($this->reservationLengthInMinutes));

        /** @var TableRepository $tableRepository */
        $tableRepository = app(TableRepository::class);

        if ($tableRepository
            ->findOverlappingReservations($table, Carbon::make($this->reservationDateTime), $reservationEndTime)
            ->count() > 0) {
            $fail('Table is not available for selected time and duration.');
        }
    }
}
