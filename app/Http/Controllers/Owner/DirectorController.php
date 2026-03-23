<?php

namespace App\Http\Controllers\Owner;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\DirectorIndexRequest;
use App\Http\Requests\Owner\StoreDirectorRequest;
use App\Http\Requests\Owner\UpdateDirectorRequest;
use App\Models\PointOfSchool;
use App\Models\Role;
use App\Models\User;
use App\Support\PointOfSchoolContext;
use App\Support\UserInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DirectorController extends Controller
{
    public function index(DirectorIndexRequest $request): Response
    {
        $owner = $request->user();
        $filters = $request->validated();
        $pointIds = PointOfSchoolContext::selectedPointIds($request, $owner);
        $assignedPointIds = $owner?->pointOfSchools()->pluck('point_of_schools.id') ?? collect();

        $directorsQuery = User::query()
            ->with([
                'pointOfSchools' => fn ($query) => $query
                    ->whereIn('point_of_schools.id', $assignedPointIds)
                    ->select('point_of_schools.id', 'point_of_schools.name'),
            ])
            ->where('school_id', $owner?->school_id)
            ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::DIRECTOR->value))
            ->whereHas('pointOfSchools', fn ($query) => $query->whereIn('point_of_schools.id', $pointIds))
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(fn ($searchQuery) => $searchQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status));

        $directors = $directorsQuery
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (User $director) => [
                'id' => $director->id,
                'name' => $director->name,
                'email' => $director->email,
                'email_verified_at' => optional($director->email_verified_at)?->toISOString(),
                'has_verified_email' => ! is_null($director->email_verified_at),
                'status' => $director->status,
                'point_of_school_ids' => $director->pointOfSchools->pluck('id')->values()->all(),
                'point_of_schools' => $director->pointOfSchools->map(fn (PointOfSchool $point) => ['id' => $point->id, 'name' => $point->name])->values()->all(),
                'last_login_at' => optional($director->last_login_at)?->format('d/m/Y H:i') ?? 'Nunca acessou',
            ]);

        return Inertia::render('owner/Directors/Index', [
            'stats' => [
                'total' => User::query()
                    ->where('school_id', $owner?->school_id)
                    ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::DIRECTOR->value))
                    ->whereHas('pointOfSchools', fn ($query) => $query->whereIn('point_of_schools.id', $pointIds))
                    ->count(),
                'active' => User::query()
                    ->where('school_id', $owner?->school_id)
                    ->where('status', 'active')
                    ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::DIRECTOR->value))
                    ->whereHas('pointOfSchools', fn ($query) => $query->whereIn('point_of_schools.id', $pointIds))
                    ->count(),
                'points' => $pointIds->count(),
            ],
            'directors' => $directors,
            'filters' => $filters,
        ]);
    }

    public function create(DirectorIndexRequest $request): Response
    {
        return Inertia::render('owner/Directors/Form', [
            'director' => null,
            ...$this->formPayload($request->user()),
        ]);
    }

    public function edit(Request $request, User $director): Response
    {
        $owner = $request->user();
        abort_unless($this->canManageDirector($owner, $director), 404);
        $allowedPointIds = $owner?->pointOfSchools()->pluck('point_of_schools.id')->map(fn ($id) => (int) $id) ?? collect();

        return Inertia::render('owner/Directors/Form', [
            'director' => [
                'id' => $director->id,
                'name' => $director->name,
                'email' => $director->email,
                'status' => $director->status,
                'point_of_school_ids' => $director->pointOfSchools()
                    ->whereIn('point_of_schools.id', $allowedPointIds)
                    ->pluck('point_of_schools.id')
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all(),
            ],
            ...$this->formPayload($owner),
        ]);
    }

    public function store(StoreDirectorRequest $request, UserInvitationService $invitationService): RedirectResponse
    {
        $owner = $request->user();
        $roleId = Role::query()->where('name', RoleEnum::DIRECTOR->value)->value('id');
        $pointIds = collect($request->validated('point_of_school_ids'))->map(fn ($id) => (int) $id)->values()->all();

        $director = User::query()->create([
            'role_id' => $roleId,
            'school_id' => $owner?->school_id,
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Str::password(16),
            'status' => $request->validated('status'),
        ]);

        $this->syncPoints($director, $pointIds, RoleEnum::DIRECTOR->label());
        $invitationService->send($director);

        return to_route('owner.directors.index')->with('success', 'Diretor criado com sucesso e convite enviado por e-mail.');
    }

    public function update(UpdateDirectorRequest $request, User $director): RedirectResponse
    {
        abort_unless($this->canManageDirector($request->user(), $director), 404);

        $pointIds = collect($request->validated('point_of_school_ids'))->map(fn ($id) => (int) $id)->values()->all();

        $director->update([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'status' => $request->validated('status'),
        ]);

        $this->syncPoints($director, $pointIds, RoleEnum::DIRECTOR->label());

        return to_route('owner.directors.index')->with('success', 'Diretor atualizado com sucesso.');
    }

    public function destroy(User $director): RedirectResponse
    {
        abort_unless($this->canManageDirector(request()->user(), $director), 404);

        $director->pointOfSchools()->detach();
        $director->delete();

        return to_route('owner.directors.index')->with('success', 'Diretor removido com sucesso.');
    }

    public function updateAccess(Request $request, User $director): RedirectResponse
    {
        abort_unless($this->canManageDirector($request->user(), $director), 404);

        $data = $request->validate([
            'status' => ['required', 'string', 'in:active,inactive'],
        ], [
            'status.required' => 'O status do diretor e obrigatorio.',
            'status.in' => 'O status informado e invalido.',
        ]);

        $director->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Acesso do diretor atualizado com sucesso.');
    }

    public function resendInvitation(Request $request, User $director, UserInvitationService $invitationService): RedirectResponse
    {
        abort_unless($this->canManageDirector($request->user(), $director), 404);

        if (! is_null($director->email_verified_at)) {
            return back()->with('info', 'Este diretor ja ativou a conta e nao precisa de um novo convite.');
        }

        $invitationService->send($director);

        return back()->with('success', 'Convite de primeiro acesso reenviado para o diretor.');
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

    private function canManageDirector(?User $owner, User $director): bool
    {
        if (! $owner || ! $director->hasRole(RoleEnum::DIRECTOR) || $director->school_id !== $owner->school_id) {
            return false;
        }

        $allowedIds = $owner->pointOfSchools()->pluck('point_of_schools.id');

        return $director->pointOfSchools()->whereIn('point_of_schools.id', $allowedIds)->exists();
    }

    private function formPayload(?User $owner): array
    {
        $pointIds = $owner?->pointOfSchools()->pluck('point_of_schools.id') ?? collect();

        return [
            'points' => PointOfSchool::query()
                ->whereIn('id', $pointIds)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (PointOfSchool $point) => ['id' => $point->id, 'name' => $point->name]),
        ];
    }
}
