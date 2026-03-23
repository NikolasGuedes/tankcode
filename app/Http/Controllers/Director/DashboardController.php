<?php

namespace App\Http\Controllers\Director;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $user = request()->user();
        $pointIds = $user?->pointOfSchools()->pluck('point_of_schools.id') ?? collect();

        return Inertia::render('director/Dashboard', [
            'stats' => [
                'classrooms' => Classroom::query()->whereIn('point_of_school_id', $pointIds)->count(),
                'teachers' => User::query()
                    ->where('school_id', $user?->school_id)
                    ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::TEACHER->value))
                    ->whereHas('pointOfSchools', fn ($query) => $query->whereIn('point_of_schools.id', $pointIds))
                    ->count(),
                'students' => User::query()
                    ->where('school_id', $user?->school_id)
                    ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::STUDENT->value))
                    ->whereHas('pointOfSchools', fn ($query) => $query->whereIn('point_of_schools.id', $pointIds))
                    ->count(),
            ],
        ]);
    }
}
