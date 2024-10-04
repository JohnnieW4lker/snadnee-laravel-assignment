<?php

namespace App\Http\Controllers\Customer;

use App\Services\TableService;
use DateTime;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class TableController extends Controller
{
    public function __construct(
        private readonly TableService $tableService
    )
    {
    }

    public function listTablesForDateTimeAndDuration(DateTime $date, int $length): AnonymousResourceCollection
    {
        return $this->tableService->getTableListWithStatusForDate($date, $length);
    }
}
