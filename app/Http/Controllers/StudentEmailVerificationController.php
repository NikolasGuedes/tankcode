<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class StudentEmailVerificationController extends Controller
{
    public function verify(Request $request, $token)
    {
        $studentId = Cache::get("student_verification_token:{$token}");

        if (!$studentId) {
            return Inertia::render('student/EmailVerificationError', [
                'message' => 'Token de verificação inválido ou expirado. Entre em contato com o professor para reenviar o email de verificação.',
            ]);
        }

        try {
            $student = Student::findOrFail($studentId);

            if ($student->hasVerifiedEmail()) {
                return redirect()->route('student.login')->with('info', 'Email já verificado. Faça login.');
            }

            DB::transaction(function () use ($student, $token) {
                $student->email_verified_at = now();
                $student->platform_access = true;
                $student->save();

                Cache::forget("student_verification_token:{$token}");
            });

            $student->refresh();

            return Inertia::render('student/CreatePassword', [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'cod' => $student->cod,
                ],
                'message' => 'Email verificado com sucesso! Agora crie sua senha para acessar a plataforma.',
            ]);
        } catch (\Throwable $th) {
            Log::error('Failed to verify email: ' . $th->getMessage());

            return Inertia::render('student/EmailVerificationError', [
                'message' => 'Erro ao verificar email. Tente novamente.',
            ]);
        }
    }

    public function createPassword(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            $student = Student::findOrFail($request->student_id);

            if (!$student->hasVerifiedEmail()) {
                return back()->withErrors(['error' => 'Você precisa verificar seu email primeiro.']);
            }

            if ($student->password) {
                return redirect()->route('student.login')->with('info', 'Senha já foi criada. Faça login.');
            }

            $student->password = bcrypt($request->password);
            $student->save();

            Log::info('Password created successfully', ['student_id' => $student->id]);

            return redirect()->route('student.login')->with('success', 'Senha criada com sucesso! Faça login para acessar a plataforma.');
        } catch (\Throwable $th) {
            Log::error('Failed to create password: ' . $th->getMessage());
            return back()->withErrors(['error' => 'Erro ao criar senha. Tente novamente.']);
        }
    }
}
