<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StudentAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se está autenticado com o guard student
        if (!Auth::guard('student')->check()) {
            return redirect()->route('student.login')->with('error', 'Você precisa estar logado para acessar esta área.');
        }

        /** @var \App\Models\Student $student */
        $student = Auth::guard('student')->user();

        // Verificar se o email foi verificado
        if (!$student->hasVerifiedEmail()) {
            Auth::guard('student')->logout();
            return redirect()->route('student.login')->with('error', 'Você precisa verificar seu email antes de acessar a plataforma. Verifique sua caixa de entrada.');
        }

        // Verificar se tem acesso à plataforma
        if (!$student->platform_access) {
            Auth::guard('student')->logout();
            return redirect()->route('student.login')->with('error', 'Seu acesso à plataforma foi desativado. Entre em contato com o administrador.');
        }

        return $next($request);
    }
}
