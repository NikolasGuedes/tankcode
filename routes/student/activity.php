<?php

use App\Http\Controllers\Student\ActivityController;
use Illuminate\Support\Facades\Route;

Route::get('/atividades/{activity}', [ActivityController::class, 'show'])->name('activities.show');
Route::post('/atividades/{activity}/respostas', [ActivityController::class, 'storeSubmission'])->name('activities.submissions.store');
