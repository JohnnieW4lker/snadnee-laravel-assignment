<?php

namespace App\Http\Requests\Reservation;

use App\Rules\TableHasEnoughSeats;
use App\Rules\TableIsAvailable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReservationCreateRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reservationDateTime' => 'required|date|after:tomorrow',
            'peopleCount' => 'required|integer|min:1',
            'reservationLengthInMinutes' => 'required|integer|min:30|max:120|multiple_of:30',
            'guestFirstName' => 'required|string|max:32',
            'guestLastName' => 'required|string|max:32',
            'tableId' => [
                'required',
                'exists:tables,id',
                new TableHasEnoughSeats($this->input('peopleCount')),
                new TableIsAvailable(
                    $this->input('reservationDateTime'),
                    $this->input('reservationLengthInMinutes')
                ),
            ],
        ];
    }
}
