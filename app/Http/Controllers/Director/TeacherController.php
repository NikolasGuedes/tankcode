<?php

namespace App\Http\Controllers\Director;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Director\StoreTeacherRequest;
use App\Http\Requests\Director\TeacherIndexRequest;
use App\Http\Requests\Director\UpdateTeacherRequest;
use App\Models\PointOfSchool;
use App\Models\Role;
use App\Models\User;
use App\Support\UserInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class TeacherController extends Controller
{
    public function index(TeacherIndexRequest $request): Response
    {
        $director = $request->user();
        $filters = $request->validated();
        $pointIds = $director?->pointOfSchools()->pluck('point_of_schools.id') ?? collect();

        $teachersQuery = User::query()
            ->with(['pointOfSchools:id,name'])
            ->withCount('classroomsAsTeacher')
            ->where('school_id', $director?->school_id)
            ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::TEACHER->value))
            ->whereHas('pointOfSchools', fn ($query) => $query->whereIn('point_of_schools.id', $pointIds))
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(fn ($searchQuery) => $searchQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['point_of_school_id'] ?? null, fn ($query, int $pointId) => $query->whereHas('pointOfSchools', fn ($pointQuery) => $pointQuery->where('point_of_schools.id', $pointId)));

        $teachers = $teachersQuery
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (User $teacher) => [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'email' => $teacher->email,
                'email_verified_at' => optional($teacher->email_verified_at)?->toISOString(),
                'has_verified_email' => ! is_null($teacher->email_verified_at),
                'status' => $teacher->status,
                'point_of_school_ids' => $teacher->pointOfSchools->pluck('id')->values()->all(),
                'point_of_schools' => $teacher->pointOfSchools->map(fn (PointOfSchool $point) => ['id' => $point->id, 'name' => $point->name])->values()->all(),
                'classrooms_count' => $teacher->classrooms_as_teacher_count,
                'last_login_at' => optional($teacher->last_login_at)?->format('d/m/Y H:i') ?? 'Nunca acessou',
            ]);

        return Inertia::render('director/Teachers/Index', [
            'stats' => [
                'total' => User::query()
                    ->where('school_id', $director?->school_id)
                    ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::TEACHER->value))
                    ->whereHas('pointOfSchools', fn ($query) => $query->whereIn('point_of_schools.id', $pointIds))
                    ->count(),
                'active' => User::query()
                    ->where('school_id', $director?->school_id)
                    ->where('status', 'active')
                    ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::TEACHER->value))
                    ->whereHas('pointOfSchools', fn ($query) => $query->whereIn('point_of_schools.id', $pointIds))
                    ->count(),
                'points' => $pointIds->count(),
            ],
            'teachers' => $teachers,
            'points' => PointOfSchool::query()
                ->whereIn('id', $pointIds)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (PointOfSchool $point) => ['id' => $point->id, 'name' => $point->name]),
            'filters' => $filters,
        ]);
    }

    public function create(TeacherIndexRequest $request): Response
    {
        return Inertia::render('director/Teachers/Form', [
            'teacher' => null,
            ...$this->formPayload($request->user()),
        ]);
    }

    public function edit(Request $request, User $teacher): Response
    {
        abort_unless($this->canManageTeacher($request->user(), $teacher), 404);

        return Inertia::render('director/Teachers/Form', [
            'teacher' => [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'email' => $teacher->email,
                'status' => $teacher->status,
                'point_of_school_ids' => $teacher->pointOfSchools()->pluck('point_of_schools.id')->values()->all(),
            ],
            ...$this->formPayload($request->user()),
        ]);
    }

    public function store(StoreTeacherRequest $request, UserInvitationService $invitationService): RedirectResponse
    {
        $director = $request->user();
        $roleId = Role::query()->where('name', RoleEnum::TEACHER->value)->value('id');
        $pointIds = collect($request->validated('point_of_school_ids'))->map(fn ($id) => (int) $id)->values()->all();

        $teacher = User::query()->create([
            'role_id' => $roleId,
            'school_id' => $director?->school_id,
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Str::password(16),
            'status' => $request->validated('status'),
        ]);

        $this->syncPoints($teacher, $pointIds, RoleEnum::TEACHER->label());
        $invitationService->send($teacher);

        return to_route('director.teachers.index')->with('success', 'Professor criado com sucesso e convite enviado por e-mail.');
    }

    public function update(UpdateTeacherRequest $request, User $teacher): RedirectResponse
    {
        abort_unless($this->canManageTeacher($request->user(), $teacher), 404);

        $pointIds = collect($request->validated('point_of_school_ids'))->map(fn ($id) => (int) $id)->values()->all();

        $teacher->update([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'status' => $request->validated('status'),
        ]);

        $this->syncPoints($teacher, $pointIds, RoleEnum::TEACHER->label());

        return to_route('director.teachers.index')->with('success', 'Professor atualizado com sucesso.');
    }

    public function destroy(User $teacher): RedirectResponse
    {
        abort_unless($this->canManageTeacher(request()->user(), $teacher), 404);

        $teacher->pointOfSchools()->detach();
        $teacher->classroomsAsTeacher()->update(['teacher_id' => null]);
        $teacher->delete();

        return to_route('director.teachers.index')->with('success', 'Professor removido com sucesso.');
    }

    public function updateAccess(Request $request, User $teacher): RedirectResponse
    {
        abort_unless($this->canManageTeacher($request->user(), $teacher), 404);

        $data = $request->validate([
            'status' => ['required', 'string', 'in:active,inactive'],
        ], [
            'status.required' => 'O status do professor e obrigatorio.',
            'status.in' => 'O status informado e invalido.',
        ]);

        $teacher->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Acesso do professor atualizado com sucesso.');
    }

    public function resendInvitation(Request $request, User $teacher, UserInvitationService $invitationService): RedirectResponse
    {
        abort_unless($this->canManageTeacher($request->user(), $teacher), 404);

        if (! is_null($teacher->email_verified_at)) {
            return back()->with('info', 'Este professor ja ativou a conta e nao precisa de um novo convite.');
        }

        $invitationService->send($teacher);

        return back()->with('success', 'Convite de primeiro acesso reenviado para o professor.');
    }

    private function syncPoints(User $user, array $pointIds, string $title): void
    {
        $payload = collect($pointIds)
            ->values()
            ->mapWithKeys(fn (int $pointId, int $index) => [
                $pointId => [
                    'title' => $title,
                    'is_primary' => $index === 0,
                    'status' => 'active',
                ],
            ])
            ->all();

        $user->pointOfSchools()->sync($payload);
    }

    private function canManageTeacher(?User $director, User $teacher): bool
    {
        if (! $director || ! $teacher->hasRole(RoleEnum::TEACHER) || $teacher->school_id !== $director->school_id) {
            return false;
        }

        $allowedIds = $director->pointOfSchools()->pluck('point_of_schools.id');

        return $teacher->pointOfSchools()->whereIn('point_of_schools.id', $allowedIds)->exists();
    }

    private function formPayload(?User $director): array
    {
        $pointIds = $director?->pointOfSchools()->pluck('point_of_schools.id') ?? collect();

        return [
            'points' => PointOfSchool::query()
                ->whereIn('id', $pointIds)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (PointOfSchool $point) => ['id' => $point->id, 'name' => $point->name]),
        ];
    }
}
