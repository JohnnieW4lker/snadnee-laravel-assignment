<?php

namespace App\Rules;

use App\Models\Table;
use App\Services\TableRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

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

        $reservationEndTime = new \DateTime($this->reservationDateTime);
        $reservationLengthInterval = new \DateInterval('');
        $reservationLengthInterval->i = $this->reservationLengthInMinutes;
        $reservationEndTime->add($reservationLengthInterval);

        $reservationEndTimeFormatted = $reservationEndTime->format(DATE_ATOM);

        /** @var TableRepository $tableRepository */
        $tableRepository = app(TableRepository::class);

        if ($tableRepository
            ->findOverlappingReservations($table, $this->reservationDateTime, $reservationEndTimeFormatted)
            ->count() > 0) {
            $fail('Table is not available for selected time and duration.');
        }
    }
}
