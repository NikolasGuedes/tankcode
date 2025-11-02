<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Mail\StudentResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class StudentPasswordResetController extends Controller
{
    public function showRequestForm()
    {
        return Inertia::render('student/ForgotPassword');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $student = Student::where('email', $request->email)
            ->whereNotNull('email_verified_at')
            ->first();

        if (!$student) {
            return back()->withErrors([
                'email' => 'Não encontramos nenhum estudante verificado com este endereço de email.',
            ]);
        }

        if (!$student->password) {
            return back()->withErrors([
                'email' => 'Você precisa criar uma senha primeiro. Verifique seu email para o link de verificação.',
            ]);
        }

        $token = Str::random(64);
        
        Cache::put(
            "student_password_reset:{$token}",
            $student->id,
            now()->addHours(1)
        );

        try {
            $resetUrl = route('student.password.reset', ['token' => $token]);
            
            Mail::to($student->email)->send(
                new StudentResetPassword($student, $resetUrl)
            );

            Log::info('Password reset email sent', [
                'student_id' => $student->id,
                'email' => $student->email,
            ]);

            return back()->with('success', 'Link de redefinição de senha enviado para seu email!');
        } catch (\Throwable $th) {
            Log::error('Failed to send password reset email: ' . $th->getMessage());
            
            return back()->withErrors([
                'email' => 'Falha ao enviar o email. Tente novamente.',
            ]);
        }
    }

    public function showResetForm(Request $request, $token)
    {
        $studentId = Cache::get("student_password_reset:{$token}");

        if (!$studentId) {
            return Inertia::render('student/EmailVerificationError', [
                'message' => 'Link de redefinição inválido ou expirado. Solicite um novo.',
            ]);
        }

        $student = Student::find($studentId);

        if (!$student) {
            return Inertia::render('student/EmailVerificationError', [
                'message' => 'Estudante não encontrado.',
            ]);
        }

        return Inertia::render('student/ResetPassword', [
            'token' => $token,
            'email' => $student->email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $studentId = Cache::get("student_password_reset:{$request->token}");

        if (!$studentId) {
            return back()->withErrors([
                'email' => 'Link de redefinição inválido ou expirado.',
            ]);
        }

        $student = Student::where('id', $studentId)
            ->where('email', $request->email)
            ->first();

        if (!$student) {
            return back()->withErrors([
                'email' => 'Não foi possível encontrar o estudante.',
            ]);
        }

        try {
            $student->password = Hash::make($request->password);
            $student->save();

            Cache::forget("student_password_reset:{$request->token}");

            Log::info('Password reset successful', [
                'student_id' => $student->id,
            ]);

            return redirect()->route('student.login')
                ->with('success', 'Senha redefinida com sucesso! Faça login com sua nova senha.');
        } catch (\Throwable $th) {
            Log::error('Failed to reset password: ' . $th->getMessage());
            
            return back()->withErrors([
                'email' => 'Erro ao redefinir senha. Tente novamente.',
            ]);
        }
    }
}
