<?php

namespace App\Http\Controllers\Customer;

use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserRegisterRequest;
use App\Http\Resources\User\MeResource;
use App\Services\AuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    public function __construct(
        private readonly AuthenticationService $authenticationService
    )
    {
    }

    public function register(UserRegisterRequest $registerRequest): MeResource
    {
        return $this->authenticationService->registerNewUser($registerRequest);
    }

    public function login(UserLoginRequest $userLoginRequest): MeResource
    {
        return $this->authenticationService->handleLogin($userLoginRequest);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authenticationService->handleLogout($request);

        return response()->json(null, Response::HTTP_OK);
    }
}
