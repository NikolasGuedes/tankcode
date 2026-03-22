<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $teacher = request()->user();
        $pointIds = $teacher?->pointOfSchools()->pluck('point_of_schools.id') ?? collect();
        $classrooms = Classroom::query()
            ->withCount('students')
            ->where('teacher_id', $teacher?->id)
            ->whereIn('point_of_school_id', $pointIds)
            ->get();

        return Inertia::render('teacher/Dashboard', [
            'stats' => [
                'points' => $pointIds->count(),
                'classrooms' => $classrooms->count(),
                'students' => $classrooms->sum('students_count'),
            ],
        ]);
    }
}
