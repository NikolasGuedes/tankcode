<?php

use App\Http\Controllers\PointOfSchoolContextController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', DashboardController::class)->name('dashboard');
Route::post('/point-of-school-context/{selection}', PointOfSchoolContextController::class)->name('point-of-school-context.switch');
