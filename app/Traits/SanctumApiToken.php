<?php

namespace App\Traits;

use Laravel\Sanctum\HasApiTokens;

trait SanctumApiToken
{
    use HasApiTokens;

    public function tokenExpired(): bool
    {
        if (! empty($this->accessToken->token->expires_at)) {
            return (bool) $this->accessToken->token->expires_at->isPast();
        }

        $oneMonthInMinutes = 60 * 24 * 30;

        return (bool) $this->accessToken->created_at->lte(now()->subMinutes($oneMonthInMinutes));
    }
}
