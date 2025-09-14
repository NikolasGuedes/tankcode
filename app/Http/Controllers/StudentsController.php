<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StudentsController extends Controller
{
    public function index()
    {
        $filters = request()->validate([
            'search' => 'nullable|string|max:255',
        ]);

        $students = Student::query()
            ->when($filters['search'] ?? false, function ($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('cod', 'like', '%' . $filters['search'] . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('student/Student', [
            'students' => $students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name, // Usa o accessor
                    'email' => $student->email,
                    'cod' => $student->cod,
                ];
            }),
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
                $student = new Student();
                $student->name = $request['name'];
                $student->email = $request['email'];
                $student->cod = $cod;
                $student->password = bcrypt('password123'); // Password padrão
                $student->save();
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
}
