<?php

namespace App\Http\Resources\Api\V1\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    public function toArray($request)
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'token' => $user->tokenWithBearer(),
            'token_expired_at' => Carbon::now()->addMinutes((int) config('sanctum.expiration')),
            'email_verified_at' => $user->email_verified_at,
        ];
    }
}
