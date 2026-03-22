<?php

namespace App\Http\Controllers\Director;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Director\StoreStudentRequest;
use App\Http\Requests\Director\StudentIndexRequest;
use App\Http\Requests\Director\UpdateStudentRequest;
use App\Models\PointOfSchool;
use App\Models\Role;
use App\Models\User;
use App\Support\UserInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function index(StudentIndexRequest $request): Response
    {
        $director = $request->user();
        $filters = $request->validated();
        $pointIds = $director?->pointOfSchools()->pluck('point_of_schools.id') ?? collect();

        $students = User::query()
            ->with(['pointOfSchools:id,name', 'classrooms:id,name'])
            ->where('school_id', $director?->school_id)
            ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::STUDENT->value))
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
            ->map(fn (User $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'status' => $student->status,
                'point_of_school_id' => $student->pointOfSchools->first()?->id,
                'point_of_school' => $student->pointOfSchools->first()?->name,
                'classroom' => $student->classrooms->first()?->name ?? '-',
                'last_login_at' => optional($student->last_login_at)?->format('d/m/Y H:i') ?? 'Nunca acessou',
            ]);

        return Inertia::render('director/Students/Index', [
            'stats' => [
                'total' => $students->count(),
                'active' => $students->where('status', 'active')->count(),
                'points' => $pointIds->count(),
            ],
            'points' => PointOfSchool::query()
                ->whereIn('id', $pointIds)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (PointOfSchool $point) => ['id' => $point->id, 'name' => $point->name]),
            'students' => $students,
        ]);
    }

    public function store(StoreStudentRequest $request, UserInvitationService $invitationService): RedirectResponse
    {
        $director = $request->user();
        $roleId = Role::query()->where('name', RoleEnum::STUDENT->value)->value('id');
        $pointId = (int) $request->validated('point_of_school_id');

        $student = User::query()->create([
            'role_id' => $roleId,
            'school_id' => $director?->school_id,
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Str::password(16),
            'status' => $request->validated('status'),
        ]);

        $student->pointOfSchools()->sync([
            $pointId => [
                'title' => RoleEnum::STUDENT->label(),
                'enrollment_code' => strtoupper(Str::random(8)),
                'is_primary' => true,
                'status' => 'active',
            ],
        ]);

        $invitationService->send($student);

        return to_route('director.students.index')->with('success', 'Aluno criado com sucesso e convite enviado por e-mail.');
    }

    public function update(UpdateStudentRequest $request, User $student): RedirectResponse
    {
        abort_unless($this->canManageStudent($request->user(), $student), 404);

        $pointId = (int) $request->validated('point_of_school_id');
        $existingCode = $student->pointOfSchools()->first()?->pivot?->enrollment_code ?? strtoupper(Str::random(8));

        $student->update([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'status' => $request->validated('status'),
        ]);

        $student->pointOfSchools()->sync([
            $pointId => [
                'title' => RoleEnum::STUDENT->label(),
                'enrollment_code' => $existingCode,
                'is_primary' => true,
                'status' => 'active',
            ],
        ]);

        return to_route('director.students.index')->with('success', 'Aluno atualizado com sucesso.');
    }

    public function destroy(User $student): RedirectResponse
    {
        abort_unless($this->canManageStudent(request()->user(), $student), 404);

        $student->pointOfSchools()->detach();
        $student->classrooms()->detach();
        $student->delete();

        return to_route('director.students.index')->with('success', 'Aluno removido com sucesso.');
    }

    private function canManageStudent(?User $director, User $student): bool
    {
        if (! $director || ! $student->hasRole(RoleEnum::STUDENT) || $student->school_id !== $director->school_id) {
            return false;
        }

        $allowedIds = $director->pointOfSchools()->pluck('point_of_schools.id');

        return $student->pointOfSchools()->whereIn('point_of_schools.id', $allowedIds)->exists();
    }
}
