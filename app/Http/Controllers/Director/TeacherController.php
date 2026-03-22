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

        $teachers = User::query()
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
            ->when($filters['point_of_school_id'] ?? null, fn ($query, int $pointId) => $query->whereHas('pointOfSchools', fn ($pointQuery) => $pointQuery->where('point_of_schools.id', $pointId)))
            ->latest()
            ->get()
            ->map(fn (User $teacher) => [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'email' => $teacher->email,
                'status' => $teacher->status,
                'point_of_school_ids' => $teacher->pointOfSchools->pluck('id')->values()->all(),
                'point_of_schools' => $teacher->pointOfSchools->map(fn (PointOfSchool $point) => ['id' => $point->id, 'name' => $point->name])->values()->all(),
                'classrooms_count' => $teacher->classrooms_as_teacher_count,
                'last_login_at' => optional($teacher->last_login_at)?->format('d/m/Y H:i') ?? 'Nunca acessou',
            ]);

        return Inertia::render('director/Teachers/Index', [
            'stats' => [
                'total' => $teachers->count(),
                'active' => $teachers->where('status', 'active')->count(),
                'points' => $pointIds->count(),
            ],
            'points' => PointOfSchool::query()
                ->whereIn('id', $pointIds)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (PointOfSchool $point) => ['id' => $point->id, 'name' => $point->name]),
            'teachers' => $teachers,
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

    private function syncPoints(User $user, array $pointIds, string $title): void
    {
        $existingPivots = $user->pointOfSchools()->withPivot(['enrollment_code'])->get()->keyBy('id');

        $payload = collect($pointIds)
            ->values()
            ->mapWithKeys(fn (int $pointId, int $index) => [
                $pointId => [
                    'title' => $title,
                    'enrollment_code' => $existingPivots->get($pointId)?->pivot?->enrollment_code ?? strtoupper(Str::random(8)),
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
}
