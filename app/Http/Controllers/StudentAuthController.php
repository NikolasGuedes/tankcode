<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use App\Models\Student;

class StudentAuthController extends Controller
{
    public function showLogin()
    {
        // Se já estiver logado, redirecionar para o dashboard
        if (session('student_id')) {
            return redirect()->route('student.dashboard');
        }
        
        return Inertia::render('student/StudentLogin');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $student = Student::where('email', $request->email)->first();

        if ($student && Hash::check($request->password, $student->password)) {
            session([
                'student_id' => $student->id,
                'student_name' => $student->name,
                'student_email' => $student->email,
                'student_cod' => $student->cod,
            ]);
            
            // Verificar se precisa trocar a senha
            if ($student->must_change_password) {
                return redirect()->route('student.change-password')->with('info', 'Por favor, altere sua senha antes de continuar.');
            }
            
            return redirect()->route('student.dashboard')->with('success', 'Login realizado com sucesso!');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não conferem com nossos registros.',
        ])->withInput($request->only('email'));
    }

    public function showChangePassword()
    {
        $studentId = session('student_id');
        
        if (!$studentId) {
            return redirect()->route('student.login');
        }

        $student = Student::find($studentId);
        
        if (!$student || !$student->must_change_password) {
            return redirect()->route('student.dashboard');
        }

        return Inertia::render('student/ChangePassword', [
            'student' => [
                'name' => $student->name,
                'email' => $student->email,
            ]
        ]);
    }

    public function changePassword(Request $request)
    {
        $studentId = session('student_id');
        
        if (!$studentId) {
            return redirect()->route('student.login');
        }

        $student = Student::find($studentId);
        
        if (!$student) {
            return redirect()->route('student.login');
        }

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $student->password)) {
            return back()->withErrors([
                'current_password' => 'A senha atual está incorreta.',
            ]);
        }

        $student->update([
            'password' => Hash::make($request->new_password),
            'password_changed_at' => now(),
            'must_change_password' => false,
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Senha alterada com sucesso!');
    }

    public function dashboard()
    {
        $studentId = session('student_id');
        
        if (!$studentId) {
            return redirect()->route('student.login')->with('error', 'Sessão expirada. Faça login novamente.');
        }

        $student = Student::find($studentId);
        
        if (!$student) {
            session()->forget(['student_id', 'student_name', 'student_email', 'student_cod']);
            return redirect()->route('student.login')->with('error', 'Estudante não encontrado.');
        }

        // Verificar se ainda precisa trocar a senha
        if ($student->must_change_password) {
            return redirect()->route('student.change-password');
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
        session()->forget(['student_id', 'student_name', 'student_email', 'student_cod']);
        return redirect()->route('home')->with('success', 'Logout realizado com sucesso!');
    }
}
