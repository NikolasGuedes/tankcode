<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class RoomsController extends Controller
{
    public function index()
    {
        return Inertia::render('rooms/rooms', []);
    }
}