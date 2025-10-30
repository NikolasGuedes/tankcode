<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Room;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class RoomsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $rooms = Room::query()
            ->when($search, function ($query, $search) {
                $query->where('name_room', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('name_room')
            ->get();

        return Inertia::render('rooms/Rooms', [
            'rooms' => $rooms,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'name_room' => 'required|string|max:255',
    ]);

    try {
        DB::transaction(function () use ($request) {
            // Gerar um código único no formato AAA-123
            do {
                $prefix = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3));
                $code = $prefix . '-' . rand(100, 999);
            } while (Room::where('code', $code)->exists());

            Room::create([
                'name_room' => $request->name_room,
                'code' => $code,
            ]);
        });
    } catch (\Throwable $th) {
        Log::error('Erro ao adicionar sala: ' . $th->getMessage(), [
            'trace' => $th->getTraceAsString(),
            'request_data' => $request->all()
        ]);

        return redirect()->back()->withErrors([
            'error' => 'Erro ao adicionar sala: ' . $th->getMessage()
        ]);
    }
    
    return redirect()->route('rooms.index')->with('success', 'Sala adicionada com sucesso!');
}

    public function update($id, Request $request)
    {
        $request->validate([
            'name_room' => 'required|string|max:255',
        ]);
    
        try {
            DB::transaction(function () use ($id, $request) {
                $room = Room::findOrFail($id);
                $room->name_room = $request->name_room;
                $room->save();
            });
        } catch (\Throwable $th) {
            Log::error('Erro ao atualizar sala: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
                'room_id' => $id,
                'request_data' => $request->all()
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Erro ao atualizar sala: ' . $th->getMessage()
            ]);
        }
        
        return redirect()->route('rooms.index')->with('success', 'Sala atualizada com sucesso!');
    }

    
    public function editrooms($id)
    {
        $filters = request()->validate([
            'search' => 'nullable|string|max:255',
        ]);

        $search = $filters['search'] ?? '';

        $room = Room::with('students')->findOrFail($id); // EAGER LOAD para ter a lista de alunos da sala

        // Lógica para pegar alunos NÃO VINCULADOS a esta sala OU VINCULADOS A ELA (para a busca)
        $students = Student::query()
            ->where(function ($query) use ($room) {
                // Seleciona alunos que não têm sala OU alunos que já estão nesta sala específica
                $query->whereNull('room_id')
                      ->orWhere('room_id', $room->id);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                             ->orWhere('email', 'like', '%' . $search . '%')
                             ->orWhere('cod', 'like', '%' . $search . '%');
                });
            })
            ->select('id', 'name', 'email', 'cod', 'room_id') // Selecionar o room_id
            ->orderBy('name')
            ->get()
            ->map(function ($student) use ($room) {
                // Adiciona uma flag para o frontend saber se o aluno já está vinculado
                $student->is_linked = $student->room_id === $room->id;
                return $student;
            });
            

        return Inertia::render('rooms/EditRooms', [
            'room' => $room,
            'students' => $students,
            'search' => $search,
        ]);
    }
    
    public function addStudent(Request $request, Room $room)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        
        // Se o aluno já estiver na sala, não faça nada ou retorne um erro.
        if ($student->room_id === $room->id) {
             return redirect()->back()->with('error', 'O aluno já está nesta sala!');
        }
        
        // VINCULA O ALUNO À NOVA SALA
        $student->room()->associate($room);
        $student->save();

        return redirect()->back()->with('success', 'Aluno adicionado à sala com sucesso!');
    }

    public function removeStudent(Request $request, Room $room, Student $student)
    {
        // Garante que o aluno está de fato na sala que está sendo editada antes de desvincular
        if ($student->room_id !== $room->id) {
            return redirect()->back()->with('error', 'O aluno não pertence a esta sala.');
        }

        // DESVINCULA O ALUNO (define room_id como NULL)
        $student->room()->dissociate();
        $student->save();

        return redirect()->back()->with('success', 'Aluno removido da sala com sucesso!');
    }
    
    // public function destroy($id, Request $request)
    // {
    //     try {
    //         DB::transaction(function () use ($id) {
    //             $room = Room::findOrFail($id);
    //             $room->delete();
    //         });
    //     } catch (\Throwable $th) {
    //         Log::error('Erro ao deletar sala: ' . $th->getMessage(), [
    //             'trace' => $th->getTraceAsString(),
    //             'room_id' => $id,
    //         ]);

    //         return redirect()->back()->withErrors([
    //             'error' => 'Erro ao deletar sala: ' . $th->getMessage()
    //         ]);
    //     }
        
    //     return redirect()->route('rooms.index')->with('success', 'Sala deletada com sucesso!');
    // }
}