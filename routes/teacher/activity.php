<?php

use App\Http\Controllers\Teacher\ActivityController;
use Illuminate\Support\Facades\Route;

Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
