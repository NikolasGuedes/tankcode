<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Classroom;
use App\Support\PointOfSchoolContext;
use Inertia\Inertia;
use Inertia\Response;

class OverviewController extends Controller
{
    public function __invoke(): Response
    {
        $request = request();
        $teacher = $request->user();
        $pointIds = PointOfSchoolContext::selectedPointIds($request, $teacher);
        $classrooms = Classroom::query()
            ->withCount('students')
            ->where('teacher_id', $teacher?->id)
            ->where('school_id', $teacher?->school_id)
            ->whereIn('point_of_school_id', $pointIds)
            ->get();

        $activitiesCount = Activity::query()
            ->where('teacher_id', $teacher?->id)
            ->whereIn('classroom_id', $classrooms->pluck('id'))
            ->count();

        return Inertia::render('teacher/Overview', [
            'stats' => [
                'points' => $pointIds->count(),
                'classrooms' => $classrooms->count(),
                'students' => $classrooms->sum('students_count'),
                'activities' => $activitiesCount,
            ],
        ]);
    }
}
