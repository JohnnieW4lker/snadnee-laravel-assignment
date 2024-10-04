<?php

namespace App\Rules;

use App\Models\Table;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TableHasEnoughSeats implements ValidationRule
{
    public function __construct(
        protected int $peopleCount
    )
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $table = Table::find($value);

        if ($table === null) {
            $fail('Table does not exist');
        }

        if ($table->seats < $this->peopleCount) {
            $fail('Table does not have enough seats.');
        }
    }
}
