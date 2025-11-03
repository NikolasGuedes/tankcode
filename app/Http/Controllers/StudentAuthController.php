<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentAuthController extends Controller
{
    public function showLogin()
    {
        // Verificar se já está autenticado com o guard student
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }
        
        return Inertia::render('student/StudentLogin');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $student = Student::where('email', $credentials['email'])->first();

        // Não revelar se o estudante existe ou não
        if (!$student || !$student->password) {
            return back()->withErrors([
                'email' => 'Credenciais inválidas.',
            ])->onlyInput('email');
        }

        // Verificar senha primeiro (não revelar outros detalhes se a senha estiver errada)
        if (!Hash::check($credentials['password'], $student->password)) {
            return back()->withErrors([
                'email' => 'Credenciais inválidas.',
            ])->onlyInput('email');
        }

        // Verificar se o email foi verificado
        if (!$student->hasVerifiedEmail()) {
            return back()->withErrors([
                'email' => 'Credenciais inválidas.',
            ])->onlyInput('email');
        }

        // Verificar se tem acesso à plataforma
        if (!$student->platform_access) {
            return back()->withErrors([
                'email' => 'Credenciais inválidas.',
            ])->onlyInput('email');
        }

        // Login usando o guard student
        Auth::guard('student')->login($student, $request->boolean('remember'));
        $request->session()->regenerate();

        Log::info('Student logged in successfully', [
            'student_id' => $student->id,
            'email' => $student->email,
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Login realizado com sucesso!');
    }

    public function dashboard()
    {
        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return redirect()->route('student.login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        return Inertia::render('student/StudentDashboard', [
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'cod' => $student->cod,
            ]
        ]);
    }

    public function logout()
    {
        Auth::guard('student')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'Logout realizado com sucesso!');
    }
}
