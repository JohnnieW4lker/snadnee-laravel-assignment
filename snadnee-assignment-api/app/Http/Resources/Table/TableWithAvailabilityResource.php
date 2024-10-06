<?php

namespace App\Http\Resources\Table;

use App\Enums\Table\TableAvailabilityEnum;
use App\Services\TableRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableWithAvailabilityResource extends JsonResource
{
    private string $reservationStartDate;
    private int $reservationLengthInMinutes;

    public function __construct($resource, string $reservationStartDate, int $reservationLengthInMinutes)
    {
        parent::__construct($resource);
        $this->reservationStartDate = $reservationStartDate;
        $this->reservationLengthInMinutes = $reservationLengthInMinutes;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tableRepository = app(TableRepository::class);

        $reservationOverlaps = $tableRepository
            ->findOverlappingReservations(
                $this->resource,
                Carbon::make($this->reservationStartDate),
                Carbon::make($this->reservationStartDate)->addMinutes($this->reservationLengthInMinutes)
            );
        $availability = $reservationOverlaps->count() > 0 ? TableAvailabilityEnum::RESERVED : TableAvailabilityEnum::AVAILABLE;

        return [
            'id' => $this->id,
            'number' => $this->number,
            'seats' => $this->seats,
            'availability' => $availability->value,
        ];
    }
}
