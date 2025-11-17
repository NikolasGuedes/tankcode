<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ActivitiesController extends Controller
{
    public function index()
    {
        return Inertia::render('Activities/Activities');
    }
}
