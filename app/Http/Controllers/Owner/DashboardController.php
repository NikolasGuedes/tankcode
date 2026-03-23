<?php

namespace App\Http\Controllers\Owner;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PointOfSchoolContext;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $request = request();
        $owner = $request->user();
        $pointIds = PointOfSchoolContext::selectedPointIds($request, $owner);

        return Inertia::render('owner/Dashboard', [
            'stats' => [
                'school' => $owner?->school?->name ?? 'Minha escola',
                'points' => $pointIds->count(),
                'directors' => User::query()
                    ->where('school_id', $owner?->school_id)
                    ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::DIRECTOR->value))
                    ->whereHas('pointOfSchools', fn ($query) => $query->whereIn('point_of_schools.id', $pointIds))
                    ->count(),
                'active_directors' => User::query()
                    ->where('school_id', $owner?->school_id)
                    ->where('status', 'active')
                    ->whereHas('role', fn ($query) => $query->where('name', RoleEnum::DIRECTOR->value))
                    ->whereHas('pointOfSchools', fn ($query) => $query->whereIn('point_of_schools.id', $pointIds))
                    ->count(),
            ],
        ]);
    }
}
