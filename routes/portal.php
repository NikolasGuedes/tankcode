<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', DashboardController::class)->name('dashboard');
Route::get('/owner', DashboardController::class)
    ->defaults('headline', 'Gestao da Escola')
    ->name('owner.dashboard');
Route::get('/teacher', DashboardController::class)
    ->defaults('headline', 'Portal do Professor')
    ->name('teacher.dashboard');
Route::get('/student', DashboardController::class)
    ->defaults('headline', 'Portal do Aluno')
    ->name('student.dashboard');
