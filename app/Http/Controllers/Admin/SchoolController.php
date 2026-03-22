<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SchoolIndexRequest;
use App\Http\Requests\Admin\StoreSchoolRequest;
use App\Http\Requests\Admin\UpdateSchoolRequest;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SchoolController extends Controller
{
    public function index(SchoolIndexRequest $request): Response
    {
        $filters = $request->validated();

        $schools = School::query()
            ->withCount('pointOfSchools')
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('cnpj', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->latest()
            ->get()
            ->map(fn (School $school) => [
                'id' => $school->id,
                'name' => $school->name,
                'cnpj' => $school->cnpj,
                'logo_url' => $school->logo_path ? Storage::url($school->logo_path) : null,
                'status' => $school->status,
                'point_of_schools_count' => $school->point_of_schools_count,
            ]);

        return Inertia::render('admin/Schools/Index', [
            'stats' => [
                'total' => School::query()->count(),
                'active' => School::query()->where('status', 'active')->count(),
                'pointOfSchools' => School::query()->withCount('pointOfSchools')->get()->sum('point_of_schools_count'),
            ],
            'schools' => $schools,
            'filters' => $filters,
        ]);
    }

    public function store(StoreSchoolRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('logo');

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('schools/logos', 'public');
        }

        School::query()->create($data);

        return to_route('admin.schools.index')->with('success', 'Escola criada com sucesso.');
    }

    public function update(UpdateSchoolRequest $request, School $school): RedirectResponse
    {
        $data = $request->safe()->except('logo');

        if ($request->hasFile('logo')) {
            if ($school->logo_path) {
                Storage::disk('public')->delete($school->logo_path);
            }

            $data['logo_path'] = $request->file('logo')->store('schools/logos', 'public');
        }

        $school->update($data);

        return to_route('admin.schools.index')->with('success', 'Escola atualizada com sucesso.');
    }

    public function destroy(School $school): RedirectResponse
    {
        DB::transaction(function () use ($school): void {
            $school->load(['users:id', 'pointOfSchools.users:id']);

            $userIds = $school->users
                ->pluck('id')
                ->merge(
                    $school->pointOfSchools
                        ->flatMap(fn ($pointOfSchool) => $pointOfSchool->users->pluck('id'))
                )
                ->unique()
                ->values();

            if ($userIds->isNotEmpty()) {
                User::query()
                    ->whereIn('id', $userIds)
                    ->forceDelete();
            }

            if ($school->pointOfSchools->isNotEmpty()) {
                $school->pointOfSchools()->forceDelete();
            }

            if ($school->logo_path) {
                Storage::disk('public')->delete($school->logo_path);
            }

            $school->forceDelete();
        });

        return to_route('admin.schools.index')->with('success', 'Escola removida com sucesso.');
    }
}
