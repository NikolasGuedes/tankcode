<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Student;

class StudentAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('student_id')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Não autorizado'], 401);
            }
            
            return redirect()->route('student.login')->with('error', 'Você precisa fazer login para acessar esta área.');
        }

        // Verificar se o estudante precisa trocar a senha (exceto nas rotas de troca de senha)
        if (!$request->routeIs('student.change-password*')) {
            $student = Student::find(session('student_id'));
            
            if ($student && $student->must_change_password) {
                return redirect()->route('student.change-password');
            }
        }

        return $next($request);
    }
}
