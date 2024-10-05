<?php

namespace Tests\Feature;

use App\Enums\Reservation\ReservationStatusEnum;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ReservationControllerTest extends TestCase
{
    use DatabaseTransactions;

    public static function provideTestListReservationsData(): array
    {
        $janNovakResolver = fn() => User::query()->where('email', '=', 'jannovak@example.com')->first();
        $helenaDoskocilovaResolver = fn() => User::query()->where('email', '=', 'helena123@example.com')->first();

        return [
            'successful listing have reservation' => [
                2,
                $janNovakResolver,
                Response::HTTP_OK
            ],
            'successful listing no reservations' => [
                0,
                $helenaDoskocilovaResolver,
                Response::HTTP_OK
            ],
            'unauthorized' => [
                0,
                null,
                Response::HTTP_UNAUTHORIZED
            ],
        ];
    }

    #[DataProvider('provideTestListReservationsData')]
    public function testListReservations(int $expectedReservationCount, $authorizedUserClosure, int $expectedResponse): void
    {
        if ($authorizedUserClosure !== null) {
            $authorizedUser = $authorizedUserClosure();
            Sanctum::actingAs($authorizedUser);
        }

        $response = $this->get('/api/v1/reservations', ['accept' => 'application/json']);

        $response->assertStatus($expectedResponse);

        if (!$response->isOk()) {
            return;
        }

        $this->assertCount($expectedReservationCount, $response->json()['data']);
    }

    public static function provideTestMakeReservationData(): array
    {
        $timeNoOverlap = Carbon::now()->addDay()->setSecond(0)->setHour(15)->setMinute(30);
        $timeYesterday = Carbon::now()->subDay()->setSecond(0)->setHour(15)->setMinute(30);
        $timeStartOverlap = Carbon::now()->addDay()->setSecond(0)->setHour(18)->setMinute(0);
        $timeEndsOverlap = Carbon::now()->addDay()->setSecond(0)->setHour(19)->setMinute(0);

        $tableOneResolver = fn() => Table::query()
            ->where('number', '=', 1)
            ->first()->id;
        $janNovakResolver = fn() => User::query()->where('email', '=', 'jannovak@example.com')->first();

        return [
            'successful reservation' => [
                [
                    'reservationDateTime' => $timeNoOverlap->toAtomString(),
                    'peopleCount' => 2,
                    'reservationLengthInMinutes' => 30,
                    'guestFirstName' => 'Jan',
                    'guestLastName' => 'Novak',
                    'tableId' =>  $tableOneResolver ,
                ],
                $janNovakResolver,
                Response::HTTP_CREATED,
            ],
            'start overlap' => [
                [
                    'reservationDateTime' => $timeStartOverlap->toAtomString(),
                    'peopleCount' => 2,
                    'reservationLengthInMinutes' => 60,
                    'guestFirstName' => 'Jan',
                    'guestLastName' => 'Novak',
                    'tableId' => $tableOneResolver,
                ],
                $janNovakResolver,
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ],
            'end overlap' => [
                [
                    'reservationDateTime' => $timeEndsOverlap->toAtomString(),
                    'peopleCount' => 2,
                    'reservationLengthInMinutes' => 60,
                    'guestFirstName' => 'Jan',
                    'guestLastName' => 'Novak',
                    'tableId' => $tableOneResolver,
                ],
                $janNovakResolver,
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ],
            'small table' => [
                [
                    'reservationDateTime' => $timeNoOverlap->toAtomString(),
                    'peopleCount' => 4,
                    'reservationLengthInMinutes' => 30,
                    'guestFirstName' => 'Jan',
                    'guestLastName' => 'Novak',
                    'tableId' => $tableOneResolver,
                ],
                $janNovakResolver,
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ],
            'date validation failure' => [
                [
                    'reservationDateTime' => $timeYesterday->toAtomString(),
                    'peopleCount' => 2,
                    'reservationLengthInMinutes' => 30,
                    'guestFirstName' => 'Jan',
                    'guestLastName' => 'Novak',
                    'tableId' => $tableOneResolver,
                ],
                $janNovakResolver,
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ],
            'length validation failure' => [
                [
                    'reservationDateTime' => $timeNoOverlap->toAtomString(),
                    'peopleCount' => 2,
                    'reservationLengthInMinutes' => 2,
                    'guestFirstName' => 'Jan',
                    'guestLastName' => 'Novak',
                    'tableId' => $tableOneResolver,
                ],
                $janNovakResolver,
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ],
            'unauthorized' => [
                [
                    'reservationDateTime' => $timeNoOverlap->toAtomString(),
                    'peopleCount' => 2,
                    'reservationLengthInMinutes' => 30,
                    'guestFirstName' => 'Jan',
                    'guestLastName' => 'Novak',
                    'tableId' => $tableOneResolver,
                ],
                null,
                Response::HTTP_UNAUTHORIZED,
            ]
        ];
    }

    #[DataProvider('provideTestMakeReservationData')]
    public function testMakeReservation(array $reservationData, $authorizedUserClosure, int $expectedResponse): void
    {
        if ($authorizedUserClosure !== null) {
            $authorizedUser = $authorizedUserClosure();
            Sanctum::actingAs($authorizedUser);
            $userReservations = $authorizedUser->reservations()->count();
        }

        $reservationData['tableId'] = $reservationData['tableId']();

        $response = $this->post('/api/v1/reservations', $reservationData, ['accept' => 'application/json']);
        $response->assertStatus($expectedResponse);

        if (!$response->isOk()){
            return;
        }

        $this->assertCount($userReservations + 1, $authorizedUser->reservations()->get());
    }

    public static function provideTestCancelReservationData(): array
    {
        $janNovakResolver = fn() => User::query()->where('email', '=', 'jannovak@example.com')->first();
        $helenaDoskocilovaResolver = fn() => User::query()->where('email', '=', 'helena123@example.com')->first();
        $activeReservationResolver = fn() => User::query()->where('email', '=', 'jannovak@example.com')->first()->reservations()->where('status', '=', ReservationStatusEnum::ACTIVE)->first()->id;
        $cancelledReservationResolver = fn() => User::query()->where('email', '=', 'jannovak@example.com')->first()->reservations()->where('status', '=', ReservationStatusEnum::CANCELLED)->first()->id;

        return [
            'successful cancellation' => [
                $activeReservationResolver,
                $janNovakResolver,
                Response::HTTP_NO_CONTENT,
            ],
            'not existing reservation' => [
                fn() => -1,
                $janNovakResolver,
                Response::HTTP_NOT_FOUND,
            ],
            'other users reservation' => [
                $activeReservationResolver,
                $helenaDoskocilovaResolver,
                Response::HTTP_FORBIDDEN,
            ],
            'rejecting dual cancellation' => [
                $cancelledReservationResolver,
                $janNovakResolver,
                Response::HTTP_NOT_FOUND,
            ],
            'unauthorized' => [
                $activeReservationResolver,
                null,
                Response::HTTP_UNAUTHORIZED,
            ],
        ];
    }

    #[DataProvider('provideTestCancelReservationData')]
    public function testCancelReservation($reservationIdResolver, $authorizedUserClosure, int $expectedResponse): void
    {
        if ($authorizedUserClosure !== null) {
            $authorizedUser = $authorizedUserClosure();
            Sanctum::actingAs($authorizedUser);
            $userReservations = $authorizedUser->reservations()->where('status', '=', ReservationStatusEnum::ACTIVE)->count();
        }

        $response = $this->delete(sprintf('/api/v1/reservations/%d', $reservationIdResolver()), headers: ['accept' => 'application/json']);

        $response->assertStatus($expectedResponse);

        if ($response->status() !== Response::HTTP_NO_CONTENT){
            return;
        }

        $this->assertCount(
            $userReservations - 1,
            $authorizedUser->reservations()->where('status', '=', ReservationStatusEnum::ACTIVE)->get()
        );
    }
}
