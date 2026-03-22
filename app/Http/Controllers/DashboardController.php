<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $user = request()->user();
        $routeName = request()->route()?->getName();

        if (! $user) {
            return to_route('login');
        }

        if ($routeName === 'dashboard') {
            return to_route($user->homeRouteName());
        }

        return to_route('settings.profile.edit')->with('info', 'Esta area ainda nao possui um painel dedicado.');
    }
}
