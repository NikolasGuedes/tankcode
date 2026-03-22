<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
        /** @var User|null $user */
        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => optional($user->email_verified_at)?->toIso8601String(),
                    'avatar' => $user->photo ? Storage::disk('public')->url($user->photo) : null,
                    'status' => $user->status,
                    'last_login_at' => optional($user->last_login_at)?->toIso8601String(),
                    'role' => $user->role ? [
                        'name' => $user->role->name?->value,
                        'label' => $user->role->label,
                    ] : null,
                    'school' => $user->school ? [
                        'id' => $user->school->id,
                        'name' => $user->school->name,
                    ] : null,
                    'home' => route($user->homeRouteName(), absolute: false),
                ] : null,
                'navigation' => $user ? $this->navigationFor($user) : [],
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'info' => fn () => $request->session()->get('info'),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    /**
     * @return array<int, array{title: string, href: string}>
     */
    private function navigationFor(User $user): array
    {
        return match ($user->role?->name) {
            RoleEnum::TANK_ADMIN => [
                ['title' => 'Visao geral', 'href' => route('admin.dashboard', absolute: false)],
                ['title' => 'Escolas', 'href' => route('admin.schools.index', absolute: false)],
                ['title' => 'Pontos de Ensino', 'href' => route('admin.point-of-schools.index', absolute: false)],
                ['title' => 'Usuarios', 'href' => route('admin.users.index', absolute: false)],
            ],
            RoleEnum::OWNER => [
                ['title' => 'Minha escola', 'href' => route('owner.dashboard', absolute: false)],
            ],
            RoleEnum::DIRECTOR => [
                ['title' => 'Visao geral', 'href' => route('director.dashboard', absolute: false)],
                ['title' => 'Turmas', 'href' => route('director.classrooms.index', absolute: false)],
                ['title' => 'Professores', 'href' => route('director.teachers.index', absolute: false)],
                ['title' => 'Alunos', 'href' => route('director.students.index', absolute: false)],
            ],
            RoleEnum::TEACHER => [
                ['title' => 'Professor', 'href' => route('teacher.dashboard', absolute: false)],
            ],
            RoleEnum::STUDENT => [
                ['title' => 'Aluno', 'href' => route('student.dashboard', absolute: false)],
            ],
            default => [
                ['title' => 'Dashboard', 'href' => route('dashboard', absolute: false)],
            ],
        };
    }
}
