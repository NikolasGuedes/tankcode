<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\StudentEmailVerification as StudentEmailVerificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StudentsController extends Controller
{
    public function index()
    {
        $filters = request()->validate([
            'search' => 'nullable|string|max:255',
        ]);

        return Inertia::render('student/Student', [
            'students' => Student::query()
            ->when($filters['search'] ?? false, function ($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                ->orWhere('cod', 'like', '%' . $filters['search'] . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(),
            'search' => $filters['search'] ?? ''
        ]);
           
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
        ]);

        // Gerar um código único no formato AAA-123 (AAA aleatório)
        do {
            $prefix = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3));
            $cod = $prefix . '-' . rand(100, 999);
        } while (Student::where('cod', $cod)->exists());

        try {
            DB::transaction(function () use ($request, $cod) {
                $student = new Student([
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'cod' => $cod,
                    'email_verified_at' => null,
                    'platform_access' => false,
                ]);
                $student->save();

                // Enviar email de verificação
                $this->sendVerificationEmail($student);
            });
        } catch (\Throwable $th) {
            Log::error('Failed to create student: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Failed to create student: ' . $th->getMessage()
            ]);
        }
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $id,
        ]);

        try {
            DB::transaction(function () use ($id, $request) {

                $student = Student::findOrFail($id);
                $student->name = $request['name'];
                $student->email = $request['email'];
                $student->save();
            });
        } catch (\Throwable $th) {
            Log::error('Failed to update student: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
                'student_id' => $id,
                'request_data' => $request->all()
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Failed to update student: ' . $th->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            $student->delete();

            return redirect()->back()->with('success', 'Estudante deletado com sucesso!');
        } catch (\Throwable $th) {
            Log::error('Failed to delete student: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
                'student_id' => $id
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Failed to delete student: ' . $th->getMessage()
            ]);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $allSheets = Excel::toArray([], $file);
        $hasErrors = false;
        $validStudents = [];

        // Primeiro loop: validar todos os dados
        foreach ($allSheets as $sheetIndex => $sheetData) {
            $rows = array_slice($sheetData, 1); // Pula o cabeçalho

            foreach ($rows as $rowIndex => $row) {
                // Pular linhas completamente vazias
                if (empty(array_filter($row, function ($value) {
                    return !is_null($value) && trim($value) !== '';
                }))) {
                    continue;
                }

                $name = isset($row[0]) ? trim($row[0]) : null;
                $email = isset($row[1]) ? trim($row[1]) : null;

                // Validar nome
                if (is_null($name) || $name === '') {
                    $hasErrors = true;
                    Log::warning('Import error: Missing name on row ' . ($rowIndex + 2), [
                        'file' => $file->getClientOriginalName(),
                        'row' => $rowIndex + 2
                    ]);
                    continue;
                }

                // Validar email
                if (is_null($email) || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $hasErrors = true;
                    Log::warning('Import error: Invalid email on row ' . ($rowIndex + 2), [
                        'file' => $file->getClientOriginalName(),
                        'name' => $name,
                        'email' => $email,
                        'row' => $rowIndex + 2
                    ]);
                    continue;
                }

                // Se chegou aqui, os dados são válidos
                $validStudents[] = [
                    'name' => $name,
                    'email' => $email
                ];
            }
        }

        if ($hasErrors) {
            throw ValidationException::withMessages([
                'msg' => 'Ocorreram erros ao importar alguns estudantes. Verifique os logs para mais detalhes.',
            ]);
        }

        if (empty($validStudents)) {
            throw ValidationException::withMessages([
                'msg' => 'Nenhum estudante válido encontrado no arquivo.',
            ]);
        }

        // Se chegou até aqui, todos os dados são válidos - pode importar
        DB::beginTransaction();
        try {
            $created = 0;
            $updated = 0;

            foreach ($validStudents as $studentData) {
                $existingStudent = Student::where('email', $studentData['email'])->first();
                
                if ($existingStudent) {
                    // Atualizar estudante existente
                    $existingStudent->update([
                        'name' => $studentData['name'],
                    ]);
                    $updated++;

                    Log::info('Student updated via import', [
                        'name' => $studentData['name'],
                        'email' => $studentData['email'],
                        'user' => Auth::user()->name ?? 'System'
                    ]);
                } else {
                    // Criar novo estudante
                    do {
                        $prefix = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3));
                        $cod = $prefix . '-' . rand(100, 999);
                    } while (Student::where('cod', $cod)->exists());

                    $student = Student::create([
                        'name' => $studentData['name'],
                        'email' => $studentData['email'],
                        'cod' => $cod,
                        'email_verified_at' => null,
                        'platform_access' => false,
                    ]);

                    // Enviar email de verificação apenas para novos estudantes
                    $this->sendVerificationEmail($student);
                    $created++;

                    Log::info('Student created via import', [
                        'name' => $studentData['name'],
                        'email' => $studentData['email'],
                        'cod' => $cod,
                        'user' => Auth::user()->name ?? 'System'
                    ]);
                }
            }

            DB::commit();
            
            $message = [];
            if ($created > 0) {
                $message[] = "{$created} estudante(s) criado(s)";
            }
            if ($updated > 0) {
                $message[] = "{$updated} estudante(s) atualizado(s)";
            }

            return redirect()->route('students')->with('success', implode(' e ', $message) . ' com sucesso!');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Failed to import students: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
            ]);

            throw ValidationException::withMessages([
                'msg' => 'Erro ao importar estudantes: ' . $th->getMessage(),
            ]);
        }
    }

    public function togglePlatformAccess($id)
    {
        try {
            $student = Student::findOrFail($id);
            
            if (!$student->hasVerifiedEmail()) {
                return back()->with('error', 'O estudante precisa verificar o email antes de ter acesso à plataforma.');
            }

            $student->platform_access = !$student->platform_access;
            $student->save();

            Log::info('Platform access toggled', [
                'student_id' => $id,
                'platform_access' => $student->platform_access,
                'user' => Auth::user()->name ?? 'System'
            ]);

            return back()->with('success', 'Acesso à plataforma atualizado com sucesso!');
        } catch (\Throwable $th) {
            Log::error('Failed to toggle platform access: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
                'student_id' => $id
            ]);

            return back()->with('error', 'Erro ao alterar acesso à plataforma.');
        }
    }

    public function sendVerificationEmail(Student $student)
    {
        // Gerar token único
        $token = Str::random(64);
        
        // Armazenar token no cache por 24 horas
        Cache::put("student_verification_token:{$token}", $student->id, now()->addHours(24));

        $verificationUrl = route('student.verify-email', ['token' => $token]);

        Mail::to($student->email)->send(
            new StudentEmailVerificationMail($student, $verificationUrl)
        );

        Log::info('Verification email sent', [
            'student_id' => $student->id,
            'email' => $student->email,
        ]);
    }

    public function downloadTemplate()
    {
        $filePath = public_path('templates/TemplateImportarAlunos.xlsx');

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Template file not found'], 404);
        }

        return response()->download($filePath, 'TemplateImportarAlunos.xlsx');
    }

    public function resendVerificationEmail($id)
    {
        try {
            $student = Student::findOrFail($id);
            
            if ($student->hasVerifiedEmail()) {
                return back()->with('error', 'Este estudante já verificou o email.');
            }

            $this->sendVerificationEmail($student);

            Log::info('Verification email resent', [
                'student_id' => $id,
                'email' => $student->email,
                'user' => Auth::user()->name ?? 'System'
            ]);

            return back()->with('success', 'Email de verificação reenviado com sucesso!');
        } catch (\Throwable $th) {
            Log::error('Failed to resend verification email: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
                'student_id' => $id
            ]);

            return back()->with('error', 'Erro ao reenviar email de verificação.');
        }
    }
}
