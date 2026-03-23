<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointOfSchool;
use App\Models\School;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'schools' => School::query()->count(),
                'pointOfSchools' => PointOfSchool::query()->count(),
                'users' => User::query()->count(),
            ],
        ]);
    }
}
