<?php

namespace App\Http\Controllers\Customer;

use App\Http\Middleware\AllowAuthenticatedOnly;
use App\Services\TableService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    public function __construct(
        private readonly TableService $tableService
    )
    {
        $this->middleware(AllowAuthenticatedOnly::class);
        $this->middleware('auth:sanctum');
    }

    public function listTablesForDateTimeAndDuration(string $date, string $length): AnonymousResourceCollection
    {
        $validatedData = Validator::make([
            'date' => $date,
            'length' => $length,
        ], [
            'date' => 'required|date|after:tomorrow',
            'length' => 'required|integer|min:30|max:120|multiple_of:30',
        ])->validate();

        return $this->tableService->getTableListWithStatusForDate($validatedData['date'], $validatedData['length']);
    }
}
