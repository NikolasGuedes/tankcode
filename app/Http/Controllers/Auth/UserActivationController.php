<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\UserInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserActivationController extends Controller
{
    public function show(string $token, UserInvitationService $invitationService): Response
    {
        $userId = Cache::get($invitationService->cacheKey($token));

        if (! $userId) {
            return Inertia::render('auth/ActivationError', [
                'message' => 'O link de ativacao e invalido ou expirou. Solicite um novo convite ao administrador.',
            ]);
        }

        $user = User::query()->with(['role:id,label', 'school:id,name'])->find($userId);

        if (! $user) {
            return Inertia::render('auth/ActivationError', [
                'message' => 'Nao foi possivel localizar a conta vinculada a este convite.',
            ]);
        }

        return Inertia::render('auth/CreatePassword', [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->label,
                'school' => $user->school?->name,
            ],
        ]);
    }

    public function store(Request $request, UserInvitationService $invitationService): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
        ]);

        $userId = Cache::get($invitationService->cacheKey($data['token']));

        if (! $userId || (int) $userId !== (int) $data['user_id']) {
            return back()->withErrors([
                'password' => 'O link de ativacao e invalido ou expirou. Solicite um novo convite.',
            ]);
        }

        $user = User::query()->find($data['user_id']);

        if (! $user) {
            return back()->withErrors([
                'password' => 'Nao foi possivel localizar a conta vinculada a este convite.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        Cache::forget($invitationService->cacheKey($data['token']));

        return to_route('login')->with('status', 'Senha criada com sucesso. Agora voce ja pode fazer login.');
    }
}
