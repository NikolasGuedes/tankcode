<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', DashboardController::class)->name('dashboard');
Route::get('/owner', DashboardController::class)->name('owner.dashboard');
Route::get('/teacher', DashboardController::class)->name('teacher.dashboard');
Route::get('/student', DashboardController::class)->name('student.dashboard');
