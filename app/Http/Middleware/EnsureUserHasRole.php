<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response|RedirectResponse
    {
        $user = $request->user();
        $allowedRoles = collect($roles)
            ->map(fn (string $role) => RoleEnum::tryFrom($role))
            ->filter()
            ->all();

        if (! $user) {
            abort(403);
        }

        if (! $user->hasRole(...$allowedRoles)) {
            return redirect()
                ->route($user->homeRouteName())
                ->with('info', 'Sua area foi atualizada conforme o perfil atual do usuario.');
        }

        return $next($request);
    }
}
