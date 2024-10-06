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

    public function getUserInfo(User $user, string $token): MeResource
    {
        return new MeResource($user, $token);
    }

    public function registerNewUser(UserRegisterRequest $registerRequest): MeResource
    {
        /** @var User $user */
        $user = User::create([
            'first_name' => $registerRequest->validated('firstName'),
            'last_name' => $registerRequest->validated('lastName'),
            'email' => $registerRequest->validated('email'),
            'phone' => $registerRequest->validated('phone'),
            'password' => bcrypt($registerRequest->validated('password')),
        ]);

        return $this->getUserInfo($user, $user->createToken('access')->plainTextToken);
    }

    public function handleLogin(UserLoginRequest $userLoginRequest): MeResource
    {
        $user = User::query()->where('email', $userLoginRequest->validated('email'))->first();

        if (!$user || !Hash::check($userLoginRequest->validated('password'), $user->password)) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        return $this->getUserInfo($user, $user->createToken('access')->plainTextToken);
    }

    public function handleLogout(Request $request): void
    {
        $request->user()->tokens()->delete();
    }
}
