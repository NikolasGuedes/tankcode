<?php

use App\Http\Controllers\Student\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'classroom'])->name('dashboard');
Route::get('/perfil', [DashboardController::class, 'profile'])->name('profile');
Route::patch('/perfil', [DashboardController::class, 'updateProfile'])->name('profile.update');
Route::get('/minha-sala', [DashboardController::class, 'classroom'])->name('classroom');
Route::get('/score-global', [DashboardController::class, 'scoreGlobal'])->name('score-global');
Route::get('/colegas/{student}', [DashboardController::class, 'show'])->name('classmates.show');
