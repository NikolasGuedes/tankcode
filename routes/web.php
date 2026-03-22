<?php

use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    require __DIR__.'/authenticated.php';

    Route::prefix('admin')->name('admin.')->middleware('role:'.RoleEnum::TANK_ADMIN->value)->group(function () {
        require __DIR__.'/admin/dashboard.php';
        require __DIR__.'/admin/school.php';
        require __DIR__.'/admin/point-of-school.php';
        require __DIR__.'/admin/user.php';
    });

    Route::prefix('director')->name('director.')->middleware('role:'.RoleEnum::DIRECTOR->value)->group(function () {
        require __DIR__.'/director/dashboard.php';
        require __DIR__.'/director/classroom.php';
        require __DIR__.'/director/teacher.php';
        require __DIR__.'/director/student.php';
    });

    require __DIR__.'/settings.php';
});
