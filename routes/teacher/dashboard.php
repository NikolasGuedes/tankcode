<?php

use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\OverviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', OverviewController::class)->name('dashboard');
Route::get('/dashboard', DashboardController::class)->name('metrics');
