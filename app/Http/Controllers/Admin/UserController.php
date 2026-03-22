<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Requests\Admin\UserIndexRequest;
use App\Models\PointOfSchool;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use App\Support\UserInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(UserIndexRequest $request): Response
    {
        $filters = $request->validated();

        $users = User::query()
            ->with(['role:id,name,label', 'school:id,name', 'pointOfSchools:id,name,school_id'])
            ->withCount('pointOfSchools')
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['role'] ?? null, fn ($query, string $role) => $query->whereHas('role', fn ($roleQuery) => $roleQuery->where('name', $role)))
            ->when($filters['school_id'] ?? null, fn ($query, int $schoolId) => $query->where('school_id', $schoolId))
            ->latest()
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'role_id' => $user->role_id,
                'school_id' => $user->school_id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => optional($user->email_verified_at)?->toISOString(),
                'has_verified_email' => ! is_null($user->email_verified_at),
                'role' => $user->role?->label,
                'role_name' => $user->role?->name?->value,
                'school' => $user->school?->name ?? 'Sistema',
                'status' => $user->status,
                'point_of_schools_count' => $user->point_of_schools_count,
                'point_of_school_ids' => $user->pointOfSchools->pluck('id')->values()->all(),
                'point_of_schools' => $user->pointOfSchools
                    ->map(fn (PointOfSchool $point) => [
                        'id' => $point->id,
                        'name' => $point->name,
                        'school_id' => $point->school_id,
                    ])
                    ->values()
                    ->all(),
                'last_login_at' => optional($user->last_login_at)?->format('d/m/Y H:i') ?? 'Nunca acessou',
            ]);

        return Inertia::render('admin/Users/Index', [
            'stats' => [
                'total' => User::query()->count(),
                'active' => User::query()->where('status', 'active')->count(),
                'leaders' => User::query()->whereHas('role', fn ($query) => $query->whereIn('name', [RoleEnum::TANK_ADMIN->value, RoleEnum::OWNER->value, RoleEnum::DIRECTOR->value]))->count(),
                'teachers' => User::query()->whereHas('role', fn ($query) => $query->where('name', RoleEnum::TEACHER->value))->count(),
                'students' => User::query()->whereHas('role', fn ($query) => $query->where('name', RoleEnum::STUDENT->value))->count(),
            ],
            'roles' => Role::query()
                ->orderBy('id')
                ->get(['id', 'name', 'label'])
                ->map(fn (Role $role) => [
                    'id' => $role->id,
                    'name' => $role->name?->value,
                    'label' => $role->label,
                ]),
            'schools' => School::query()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (School $school) => [
                    'id' => $school->id,
                    'name' => $school->name,
                ]),
            'points' => PointOfSchool::query()
                ->orderBy('name')
                ->get(['id', 'school_id', 'name'])
                ->map(fn (PointOfSchool $point) => [
                    'id' => $point->id,
                    'school_id' => $point->school_id,
                    'name' => $point->name,
                ]),
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    public function store(StoreUserRequest $request, UserInvitationService $userInvitationService): RedirectResponse
    {
        $data = $request->validated();
        $pointIds = collect($data['point_of_school_ids'] ?? [])->filter()->map(fn ($pointId) => (int) $pointId)->values();
        unset($data['point_of_school_ids']);
        $data['password'] = Str::password(16);
        $data['email_verified_at'] = null;

        $role = Role::query()->find($data['role_id']);

        if ($role?->name === RoleEnum::TANK_ADMIN) {
            $data['school_id'] = null;
            $pointIds = collect();
        }

        $user = User::query()->create($data);
        $this->syncPointOfSchools($user, $pointIds->all(), $role?->label ?? 'Usuario');
        $userInvitationService->send($user);

        return to_route('admin.users.index')->with('success', 'Usuario criado com sucesso e convite enviado por e-mail.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $pointIds = collect($data['point_of_school_ids'] ?? [])->filter()->map(fn ($pointId) => (int) $pointId)->values();
        unset($data['point_of_school_ids']);

        $role = Role::query()->find($data['role_id']);

        if ($role?->name === RoleEnum::TANK_ADMIN) {
            $data['school_id'] = null;
            $pointIds = collect();
        }

        $user->update($data);
        $this->syncPointOfSchools($user, $pointIds->all(), $role?->label ?? 'Usuario');

        return to_route('admin.users.index')->with('success', 'Usuario atualizado com sucesso.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->pointOfSchools()->detach();
        $user->delete();

        return to_route('admin.users.index')->with('success', 'Usuario removido com sucesso.');
    }

    public function updateAccess(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:active,inactive'],
        ], [
            'status.required' => 'O status do usuario e obrigatorio.',
            'status.in' => 'O status informado e invalido.',
        ]);

        $user->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Acesso a plataforma atualizado com sucesso.');
    }

    public function resendInvitation(User $user, UserInvitationService $userInvitationService): RedirectResponse
    {
        if (! is_null($user->email_verified_at)) {
            return back()->with('info', 'Este usuario ja ativou a conta e nao precisa de um novo convite.');
        }

        $userInvitationService->send($user);

        return back()->with('success', 'Convite de primeiro acesso reenviado com sucesso.');
    }

    protected function syncPointOfSchools(User $user, array $pointIds, string $title): void
    {
        if ($pointIds === []) {
            $user->pointOfSchools()->detach();

            return;
        }

        $existingPivots = $user->pointOfSchools()
            ->withPivot(['enrollment_code'])
            ->get()
            ->keyBy('id');

        $syncPayload = collect($pointIds)
            ->values()
            ->mapWithKeys(function (int $pointId, int $index) use ($existingPivots, $title) {
                $existingPivot = $existingPivots->get($pointId)?->pivot;

                return [
                    $pointId => [
                        'title' => $title,
                        'enrollment_code' => $existingPivot?->enrollment_code ?? strtoupper(Str::random(8)),
                        'is_primary' => $index === 0,
                        'status' => 'active',
                    ],
                ];
            })
            ->all();

        $user->pointOfSchools()->sync($syncPayload);
    }
}
