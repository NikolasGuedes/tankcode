<?php

namespace App\Http\Controllers;

use App\Support\PointOfSchoolContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PointOfSchoolContextController extends Controller
{
    public function __invoke(Request $request, string $selection): RedirectResponse
    {
        if (! PointOfSchoolContext::set($request, $request->user(), $selection)) {
            return back()->with('error', 'O ponto de ensino selecionado nao esta disponivel para este usuario.');
        }

        return back()->with('success', 'Contexto de ponto de ensino atualizado com sucesso.');
    }
}
