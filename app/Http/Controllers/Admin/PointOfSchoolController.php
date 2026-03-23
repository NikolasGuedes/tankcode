<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PointOfSchoolIndexRequest;
use App\Http\Requests\Admin\StorePointOfSchoolRequest;
use App\Http\Requests\Admin\UpdatePointOfSchoolRequest;
use App\Models\PointOfSchool;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PointOfSchoolController extends Controller
{
    public function index(PointOfSchoolIndexRequest $request): Response
    {
        $filters = $request->validated();

        $pointsQuery = PointOfSchool::query()
            ->with('school:id,name')
            ->withCount('users')
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('cnpj', 'like', "%{$search}%")
                        ->orWhere('zip_code', 'like', "%{$search}%")
                        ->orWhere('address_line', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['school_id'] ?? null, fn ($query, int $schoolId) => $query->where('school_id', $schoolId));

        $points = $pointsQuery
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (PointOfSchool $point) => [
                'id' => $point->id,
                'name' => $point->name,
                'school' => $point->school?->name,
                'school_id' => $point->school_id,
                'status' => $point->status,
                'cnpj' => $point->cnpj,
                'zip_code' => $point->zip_code,
                'users_count' => $point->users_count,
                'address_line' => $point->address_line,
            ]);

        return Inertia::render('admin/PointOfSchools/Index', [
            'stats' => [
                'total' => PointOfSchool::query()->count(),
                'active' => PointOfSchool::query()->where('status', 'active')->count(),
                'schools' => PointOfSchool::query()->distinct('school_id')->count('school_id'),
                'users' => PointOfSchool::query()->withCount('users')->get()->sum('users_count'),
            ],
            'schools' => School::query()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (School $school) => [
                    'id' => $school->id,
                    'name' => $school->name,
                ]),
            'points' => $points,
            'filters' => $filters,
        ]);
    }

    public function store(StorePointOfSchoolRequest $request): RedirectResponse
    {
        PointOfSchool::query()->create($request->validated());

        return to_route('admin.point-of-schools.index')->with('success', 'Ponto de ensino criado com sucesso.');
    }

    public function update(UpdatePointOfSchoolRequest $request, PointOfSchool $pointOfSchool): RedirectResponse
    {
        $pointOfSchool->update($request->validated());

        return to_route('admin.point-of-schools.index')->with('success', 'Ponto de ensino atualizado com sucesso.');
    }

    public function destroy(PointOfSchool $pointOfSchool): RedirectResponse
    {
        $pointOfSchool->users()->detach();
        $pointOfSchool->delete();

        return to_route('admin.point-of-schools.index')->with('success', 'Ponto de ensino removido com sucesso.');
    }
}
