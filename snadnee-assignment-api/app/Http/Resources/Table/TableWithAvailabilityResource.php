<?php

namespace App\Http\Resources\Table;

use App\Enums\Table\TableAvailabilityEnum;
use App\Services\TableRepository;
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

        $reservationOverlaps = $tableRepository->findOverlappingReservations($this->resource, $this->reservationStartDate, $this->reservationLengthInMinutes);
        $availability = $reservationOverlaps->count() > 0 ? TableAvailabilityEnum::RESERVED : TableAvailabilityEnum::AVAILABLE;

        return [
            'number' => $this->number,
            'chairs' => $this->chairs,
            'availability' => $availability->value,
        ];
    }
}
