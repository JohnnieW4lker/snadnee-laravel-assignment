<?php

namespace App\Services;

use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserRegisterRequest;
use App\Http\Resources\User\MeResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationService
{

    public function getUserInfo(User $user): MeResource
    {
        return MeResource::make($user);
    }

    public function registerNewUser(UserRegisterRequest $registerRequest): MeResource
    {
        /** @var User $user */
        $user = User::create([
            'first_name' => $registerRequest->validated('firstName'),
            'last_name' => $registerRequest->validated('lastName'),
            'email' => $registerRequest->validated('email'),
            'phone' => $registerRequest->validated('email'),
            'password' => bcrypt($registerRequest->validated('password')),
        ]);
        $user->createToken('access');

        return $this->getUserInfo($user);
    }

    public function handleLogin(UserLoginRequest $userLoginRequest): MeResource
    {
        $user = User::query()->where('email', $userLoginRequest->validated('email'))->first();

        if (!$user || !Hash::check($userLoginRequest->validated('password'), $user->password)) {
            abort(Response::HTTP_UNAUTHORIZED);
        }
        $user->createToken('access');

        return $this->getUserInfo($user);
    }

    public function handleLogout(Request $request): void
    {
        $request->user()->tokens()->delete();
    }
}
