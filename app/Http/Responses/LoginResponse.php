<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse|JsonResponse
    {
        /** @var Request $request */
        /** @var User|null $user */
        $user = $request->user();

        if ($user) {
            $user->forceFill([
                'last_login_at' => now(),
            ])->save();
        }

        $redirect = route($user?->homeRouteName() ?? 'dashboard', absolute: false);

        if ($request->wantsJson()) {
            return new JsonResponse([
                'redirect' => $redirect,
            ]);
        }

        return redirect()->intended($redirect);
    }
}
