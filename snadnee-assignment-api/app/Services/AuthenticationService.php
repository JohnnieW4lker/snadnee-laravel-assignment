<?php

namespace App\Services;

use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserRegisterRequest;
use App\Http\Resources\User\MeResource;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationService
{

    public function getCurrentUserInfo(): MeResource
    {
        return MeResource::make(auth()->user());
    }

    public function registerNewUser(UserRegisterRequest $registerRequest): MeResource
    {
        /** @var User $user */
        $user = User::create([
            'firstName' => $registerRequest->validated('firstName'),
            'lastName' => $registerRequest->validated('lastName'),
            'email' => $registerRequest->validated('email'),
            'phone' => $registerRequest->validated('email'),
            'password' => bcrypt($registerRequest->validated('password')),
        ]);

        auth()->login($user);

        return $this->getCurrentUserInfo();
    }

    public function handleLogin(UserLoginRequest $userLoginRequest): MeResource
    {
        $token = auth()->attempt($userLoginRequest->validated());
        if ($token === true) {
            return $this->getCurrentUserInfo();
        }

        abort(Response::HTTP_FORBIDDEN);
    }

    public function handleLogout(): void
    {
        auth()->logout();
    }
}
