<?php

namespace App\Support;

use App\Mail\UserInvitationMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserInvitationService
{
    public function send(User $user): void
    {
        $token = Str::random(64);

        Cache::put($this->cacheKey($token), $user->id, now()->addHours(24));

        Mail::to($user->email)->send(
            new UserInvitationMail($user, route('activation.show', ['token' => $token])),
        );
    }

    public function cacheKey(string $token): string
    {
        return "user_activation_token:{$token}";
    }
}
