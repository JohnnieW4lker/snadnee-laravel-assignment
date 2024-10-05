<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TableControllerTest extends TestCase
{
    use DatabaseTransactions;

    public static function provideTestListTablesForDateTimeAndDurationData(): array
    {
        $janNovakResolver = fn() => User::query()->where('email', '=', 'jannovak@example.com')->first();
        $testDateToday = Carbon::now()->addDay()->setHour(15)->setMinute(30);
        $testDateYesterday = Carbon::now()->subDay()->setHour(15)->setMinute(30);

        return [
            'successful listing' => [
                [
                  'date' => $testDateToday->toAtomString(),
                  'length' => 30,
                ],
                $janNovakResolver,
                Response::HTTP_OK,
            ],
            'date validation failure' => [
                [
                    'date' => $testDateYesterday->toAtomString(),
                    'length' => 30,
                ],
                $janNovakResolver,
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ],
            'length validation failure' => [
                [
                    'date' => $testDateToday->toAtomString(),
                    'length' => 12,
                ],
                $janNovakResolver,
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ],
            'unauthorized' => [
                [
                    'date' => $testDateToday->toAtomString(),
                    'length' => 30,
                ],
                null,
                Response::HTTP_UNAUTHORIZED,
            ],
        ];
    }

    #[DataProvider('provideTestListTablesForDateTimeAndDurationData')]
    public function testListTablesForDateTimeAndDuration(array $queryData, $authorizedUserClosure, int $expectedResponse): void
    {
        if ($authorizedUserClosure !== null) {
            $authorizedUser = $authorizedUserClosure();
            Sanctum::actingAs($authorizedUser);
        }

        $response = $this->get(sprintf('/api/v1/tables/date/%s/length/%d', $queryData['date'], $queryData['length']), ['accept' => 'application/json']);

        $response->assertStatus($expectedResponse);

        if (!$response->isOk()) {
            return;
        }

        $this->assertCount(6, $response->json()['data']);
    }
}
