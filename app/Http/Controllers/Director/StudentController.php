<?php

namespace App\Http\Controllers\Director;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Director\StoreStudentRequest;
use App\Http\Requests\Director\StudentIndexRequest;
use App\Http\Requests\Director\UpdateStudentRequest;
use App\Models\Classroom;
use App\Models\PointOfSchool;
use App\Models\Role;
use App\Models\User;
use App\Support\UserInvitationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
                'email_verified_at' => optional($student->email_verified_at)?->toISOString(),
                'has_verified_email' => ! is_null($student->email_verified_at),
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
            'classrooms' => Classroom::query()
                ->where('school_id', $director?->school_id)
                ->whereIn('point_of_school_id', $pointIds)
                ->orderBy('name')
                ->get(['id', 'point_of_school_id', 'name', 'code'])
                ->map(fn (Classroom $classroom) => [
                    'id' => $classroom->id,
                    'point_of_school_id' => $classroom->point_of_school_id,
                    'name' => $classroom->name,
                    'code' => $classroom->code,
                ]),
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

        $student->update([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'status' => $request->validated('status'),
        ]);

        $student->pointOfSchools()->sync([
            $pointId => [
                'title' => RoleEnum::STUDENT->label(),
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

    public function updateAccess(Request $request, User $student): RedirectResponse
    {
        abort_unless($this->canManageStudent($request->user(), $student), 404);

        $data = $request->validate([
            'status' => ['required', 'string', 'in:active,inactive'],
        ], [
            'status.required' => 'O status do aluno e obrigatorio.',
            'status.in' => 'O status informado e invalido.',
        ]);

        $student->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Acesso do aluno atualizado com sucesso.');
    }

    public function resendInvitation(Request $request, User $student, UserInvitationService $invitationService): RedirectResponse
    {
        abort_unless($this->canManageStudent($request->user(), $student), 404);

        if (! is_null($student->email_verified_at)) {
            return back()->with('info', 'Este aluno ja ativou a conta e nao precisa de um novo convite.');
        }

        $invitationService->send($student);

        return back()->with('success', 'Convite de primeiro acesso reenviado para o aluno.');
    }

    public function importStudents(Request $request, UserInvitationService $invitationService): RedirectResponse
    {
        $director = $request->user();

        $data = $request->validate([
            'point_of_school_id' => ['required', 'integer', 'exists:point_of_schools,id'],
            'classroom_id' => ['required', 'integer', 'exists:classrooms,id'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt'],
        ], [
            'point_of_school_id.required' => 'Selecione o ponto de ensino para importar os alunos.',
            'classroom_id.required' => 'Selecione a turma para importar os alunos.',
            'file.required' => 'Selecione um arquivo para importar.',
            'file.mimes' => 'Envie um arquivo XLSX, XLS ou CSV.',
        ]);

        if (! $this->pointBelongsToDirector($director, (int) $data['point_of_school_id'])) {
            return back()->withErrors([
                'point_of_school_id' => 'Selecione um ponto de ensino disponivel para a diretoria.',
            ]);
        }

        $classroom = Classroom::query()->find((int) $data['classroom_id']);

        if (! $classroom || ! $this->canManageClassroom($director, $classroom) || $classroom->point_of_school_id !== (int) $data['point_of_school_id']) {
            return back()->withErrors([
                'classroom_id' => 'Selecione uma turma vinculada ao ponto de ensino informado.',
            ]);
        }

        $importPayload = $this->extractImportRows($request->file('file'));

        if (! $importPayload['valid_template']) {
            return back()->withErrors([
                'file' => 'Use o template padrao com as colunas NOME e EMAIL.',
            ]);
        }

        $rows = $importPayload['rows'];

        if ($rows === []) {
            return back()->withErrors([
                'file' => 'Nenhum aluno valido foi encontrado no arquivo enviado.',
            ]);
        }

        $validationErrors = $this->validateImportRows($rows, $director);

        if ($validationErrors !== []) {
            return back()->withErrors([
                'file' => implode(' ', $validationErrors),
            ]);
        }

        $roleId = Role::query()->where('name', RoleEnum::STUDENT->value)->value('id');
        $pointId = (int) $data['point_of_school_id'];
        $classroomId = (int) $data['classroom_id'];
        $emails = collect($rows)->pluck('email');
        $existingUsers = User::query()
            ->with(['role:id,name'])
            ->whereIn('email', $emails)
            ->get()
            ->keyBy(fn (User $user) => Str::lower($user->email));

        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($rows, $existingUsers, $roleId, $director, $pointId, $classroomId, $invitationService, &$created, &$updated): void {
            foreach ($rows as $row) {
                $emailKey = Str::lower($row['email']);
                /** @var User|null $student */
                $student = $existingUsers->get($emailKey);

                if ($student) {
                    $student->update([
                        'name' => $row['name'],
                    ]);

                    $updated++;
                } else {
                    $student = User::query()->create([
                        'role_id' => $roleId,
                        'school_id' => $director?->school_id,
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'password' => Str::password(16),
                        'status' => 'active',
                    ]);

                    $invitationService->send($student);
                    $existingUsers->put($emailKey, $student->load('role:id,name'));
                    $created++;
                }

                $this->syncStudentPoint($student, $pointId);
                $student->classrooms()->sync([$classroomId]);
            }
        });

        $parts = [];

        if ($created > 0) {
            $parts[] = "{$created} aluno(s) criado(s)";
        }

        if ($updated > 0) {
            $parts[] = "{$updated} aluno(s) atualizado(s)";
        }

        return to_route('director.students.index')->with('success', implode(' e ', $parts).' com sucesso.');
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        $filePath = public_path('templates/TemplateImportarAlunos.xlsx');

        abort_unless(file_exists($filePath), 404);

        return response()->download($filePath, 'TemplateImportarAlunos.xlsx');
    }

    private function canManageStudent(?User $director, User $student): bool
    {
        if (! $director || ! $student->hasRole(RoleEnum::STUDENT) || $student->school_id !== $director->school_id) {
            return false;
        }

        $allowedIds = $director->pointOfSchools()->pluck('point_of_schools.id');

        return $student->pointOfSchools()->whereIn('point_of_schools.id', $allowedIds)->exists();
    }

    private function pointBelongsToDirector(?User $director, int $pointId): bool
    {
        if (! $director) {
            return false;
        }

        return $director->pointOfSchools()->whereKey($pointId)->exists();
    }

    private function extractImportRows(?UploadedFile $file): array
    {
        if (! $file) {
            return [
                'valid_template' => false,
                'rows' => [],
            ];
        }

        $sheets = Excel::toArray([], $file);
        $rows = $sheets[0] ?? [];
        $header = collect($rows[0] ?? [])->map(fn ($value) => Str::upper(trim((string) $value)))->values();

        if ($header->take(2)->all() !== ['NOME', 'EMAIL']) {
            return [
                'valid_template' => false,
                'rows' => [],
            ];
        }

        return [
            'valid_template' => true,
            'rows' => collect(array_slice($rows, 1))
                ->map(fn (array $row, int $index) => [
                    'line' => $index + 2,
                    'name' => isset($row[0]) ? trim((string) $row[0]) : '',
                    'email' => isset($row[1]) ? trim((string) $row[1]) : '',
                ])
                ->filter(fn (array $row) => $row['name'] !== '' || $row['email'] !== '')
                ->values()
                ->all(),
        ];
    }

    private function validateImportRows(array $rows, ?User $director): array
    {
        $errors = [];
        $seenEmails = [];

        foreach ($rows as $row) {
            if ($row['name'] === '') {
                $errors[] = "A linha {$row['line']} esta sem o nome do aluno.";
            }

            if ($row['email'] === '' || ! filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "A linha {$row['line']} possui um e-mail invalido.";
                continue;
            }

            $emailKey = Str::lower($row['email']);

            if (isset($seenEmails[$emailKey])) {
                $errors[] = "O e-mail {$row['email']} esta duplicado no arquivo.";
                continue;
            }

            $seenEmails[$emailKey] = true;

            $existingUser = User::query()
                ->with('role:id,name')
                ->where('email', $row['email'])
                ->first();

            if (! $existingUser) {
                continue;
            }

            if (! $existingUser->hasRole(RoleEnum::STUDENT) || $existingUser->school_id !== $director?->school_id) {
                $errors[] = "O e-mail {$row['email']} ja esta em uso por outra conta do sistema.";
                continue;
            }

            if (! $this->canManageStudent($director, $existingUser)) {
                $errors[] = "O aluno com e-mail {$row['email']} nao pertence ao escopo desta diretoria.";
            }
        }

        return $errors;
    }

    private function syncStudentPoint(User $student, int $pointId): void
    {
        $student->pointOfSchools()->sync([
            $pointId => [
                'title' => RoleEnum::STUDENT->label(),
                'is_primary' => true,
                'status' => 'active',
            ],
        ]);
    }

    private function canManageClassroom(?User $director, Classroom $classroom): bool
    {
        if (! $director || $classroom->school_id !== $director->school_id) {
            return false;
        }

        return $director->pointOfSchools()->where('point_of_schools.id', $classroom->point_of_school_id)->exists();
    }
}
