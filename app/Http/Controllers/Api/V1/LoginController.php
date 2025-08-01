<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Resources\Api\V1\Auth\LoginResource;
use App\Models\User;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        /** @var User $user */
        $user = User::where('email', $request->input('email'))->first();
        $user->tokens()->delete();

        return $this->ok(__('auth.you_are_logged_in'), LoginResource::make($user));
    }
}
