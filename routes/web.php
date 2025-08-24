<?php

use App\Http\Controllers\StudentsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::prefix('students')->middleware('auth')->group(function () {
   Route::get('/', [StudentsController::class, 'index'])->middleware('auth')->name('students');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
