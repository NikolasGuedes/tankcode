<?php

use App\Http\Controllers\Teacher\ActivityController;
use Illuminate\Support\Facades\Route;

Route::resource('/activities', ActivityController::class)
    ->only(['index', 'store', 'update', 'destroy']);
