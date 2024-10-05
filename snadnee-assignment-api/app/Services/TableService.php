<?php

namespace App\Services;

use App\Http\Resources\Table\TableWithAvailabilityResource;
use App\Models\Table;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TableService
{

    public function getTableListWithStatusForDate(string $date, int $length): AnonymousResourceCollection
    {
        $allTables = Table::all();

        $resourceCollection = $allTables->map(function ($table) use ($date, $length) {
            return new TableWithAvailabilityResource($table, $date, $length);
        });

        return new AnonymousResourceCollection($resourceCollection, TableWithAvailabilityResource::class);
    }
}
