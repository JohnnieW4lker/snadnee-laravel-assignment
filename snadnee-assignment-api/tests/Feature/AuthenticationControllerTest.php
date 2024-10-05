<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthenticationControllerTest extends TestCase
{
    use DatabaseTransactions;

    public static function provideTestLoginData(): array
    {
        $janNovakResolver = fn() => User::query()->where('email', '=', 'jannovak@example.com')->first();
        return [
            'successful login' => [
                [
                    'email' => 'jannovak@example.com',
                    'password' => 'Password12345'
                ],
                null,
                Response::HTTP_OK,
            ],
            'wrong password' => [
                [
                    'email' => 'jannovak@example.com',
                    'password' => 'jasdjiadio#33242ko'
                ],
                null,
                Response::HTTP_UNAUTHORIZED,
            ],
            'wrong email' => [
                [
                    'email' => 'abcdef@example.com',
                    'password' => 'Password12345'
                ],
                null,
                Response::HTTP_UNAUTHORIZED,
            ],
            'email validation error' => [
                [
                    'email' => 'asdle.com',
                    'password' => 'Password12345'
                ],
                null,
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ],
            'double login rejection' => [
                [
                    'email' => 'jannovak@example.com',
                    'password' => 'Password12345'
                ],
                $janNovakResolver,
                Response::HTTP_FORBIDDEN,
            ],
        ];
    }

    #[DataProvider('provideTestLoginData')]
    public function testLogin(array $loginData, $authorizedUserClosure, int $expectedResponse): void
    {
        if ($authorizedUserClosure !== null) {
            $authorizedUser = $authorizedUserClosure();
            Sanctum::actingAs($authorizedUser);
        }

        $response = $this->post('/api/v1/auth/login', $loginData,  ['accept' => 'application/json']);

        $response->assertStatus($expectedResponse);
    }

    public static function provideTestLogoutData(): array
    {
        $janNovakResolver = fn() => User::query()->where('email', '=', 'jannovak@example.com')->first();
        return [
            'successful logout' => [
                $janNovakResolver,
                Response::HTTP_OK,
            ],
            'unauthorized logout' => [
                null,
                Response::HTTP_UNAUTHORIZED,
            ],
        ];
    }

    #[DataProvider('provideTestLogoutData')]
    public function testLogout($authorizedUserClosure, int $expectedResponse): void
    {
        if ($authorizedUserClosure !== null) {
            $authorizedUser = $authorizedUserClosure();
            Sanctum::actingAs($authorizedUser);
        }

        $response = $this->post('/api/v1/auth/logout', headers: ['accept' => 'application/json']);

        $response->assertStatus($expectedResponse);

        if (!$response->isOk()) {
            return;
        }

        $this->assertCount(0, $authorizedUser->tokens()->get());
    }

    public static function provideTestRegisterData(): array
    {
        $janNovakResolver = fn() => User::query()->where('email', '=', 'jannovak@example.com')->first();
        return [
            'successful registration' => [
                [
                    'firstName' => 'Leoš',
                    'lastName' => 'Dostál',
                    'email' => 'dostal@example.com',
                    'phone' => '+420666444333',
                    'password' => 'SuppaStrongPassword12345'
                ],
                null,
                Response::HTTP_CREATED,
            ],
            'duplicate email' => [
                [
                    'firstName' => 'Leoš',
                    'lastName' => 'Dostál',
                    'email' => 'jannovak@example.com',
                    'phone' => '+420666444333',
                    'password' => 'SuppaStrongPassword12345'
                ],
                null,
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ],
            'weak password' => [
                [
                    'firstName' => 'Leoš',
                    'lastName' => 'Dostál',
                    'email' => 'dostal@example.com',
                    'phone' => '+420666444333',
                    'password' => '1234'
                ],
                null,
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ],
            'rejecting double registration' => [
                [
                    'firstName' => 'Leoš',
                    'lastName' => 'Dostál',
                    'email' => 'dostal@example.com',
                    'phone' => '+420666444333',
                    'password' => 'SuppaStrongPassword12345'
                ],
                $janNovakResolver,
                Response::HTTP_FORBIDDEN,
            ],
            'invalid phone number format' => [
                [
                    'firstName' => 'Leoš',
                    'lastName' => 'Dostál',
                    'email' => 'dostal@example.com',
                    'phone' => '323425234',
                    'password' => 'SuppaStrongPassword12345'
                ],
                null,
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ],
        ];
    }

    #[DataProvider('provideTestRegisterData')]
    public function testRegister(array $registrationData, $authorizedUserClosure, int $expectedResponse): void
    {
        if ($authorizedUserClosure !== null) {
            $authorizedUser = $authorizedUserClosure();
            Sanctum::actingAs($authorizedUser);
        }

        $response = $this->post('/api/v1/auth/register', $registrationData, ['accept' => 'application/json']);

        $response->assertStatus($expectedResponse);

        if (!$response->isOk()) {
            return;
        }

        $this->assertCount(1, User::query()->where('email', '=', $registrationData['email'])->get());
    }
}
