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
                $query->where('first_name', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('last_name', 'like', '%' . $filters['search'] . '%')
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
            'cod' => 'required|string|max:255|unique:students,cod',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Separar o nome em first_name e last_name
                $nameParts = explode(' ', $request['name'], 2);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

                Student::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $request['email'],
                    'cod' => $request['cod'],
                    'password' => bcrypt('password'), // Password padrão
                ]);
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
            'cod' => 'required|string|max:255|unique:students,cod,' . $id,
        ]);

        try {
            DB::transaction(function () use ($id, $request) {
                // Separar o nome em first_name e last_name
                $nameParts = explode(' ', $request['name'], 2);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

                Student::where('id', $id)->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $request['email'],
                    'cod' => $request['cod'],
                ]);
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
