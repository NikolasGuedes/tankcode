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
            ->paginate(10);

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
            // Adicionamos 'filter' com um valor padrão
            'filter' => 'nullable|string|in:linked,unlinked',
        ]);

        $search = $filters['search'] ?? '';
        $filter = $filters['filter'] ?? 'linked'; // Define 'linked' como padrão
        

        $room = Room::with('students')->findOrFail($id); 

        // Construir a query base
        $studentsQuery = Student::query()
            ->whereNotNull('email_verified_at');
            
        // Aplicar os filtros com base na aba selecionada
        if ($filter === 'linked') {
            $studentsQuery->where('room_id', $room->id);
        } else {
            $studentsQuery->whereNull('room_id');
        }

        // Aplicar busca se houver
        if ($search) {
            $studentsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('cod', 'like', '%' . $search . '%');
            });
        }

        // Selecionar campos necessários
        $studentsQuery->select('id', 'name', 'email', 'cod', 'room_id');

        // Obtém a paginação dos alunos
        $students = $studentsQuery->orderBy('name')->paginate(10);

        // Mapeia os resultados após a paginação
        $students->through(function ($student) use ($room) {
            $student->is_linked = $student->room_id === $room->id;
            return $student;
        });

        // Calcula os totais independentemente da paginação
        $totalLinked = Student::whereNotNull('email_verified_at')
            ->where('room_id', $room->id)
            ->count();

        $totalUnlinked = Student::whereNotNull('email_verified_at')
            ->whereNull('room_id')
            ->count();

        // Retorna com os totais e a paginação para ambas as abas
        return Inertia::render('rooms/EditRooms', [
            'room' => $room,
            'students' => $students,
            'search' => $search,
            'filter' => $filter,
            'totalLinked' => $totalLinked,
            'totalUnlinked' => $totalUnlinked,
        ]);
            

        return Inertia::render('rooms/EditRooms', [
            'room' => $room,
            'students' => $students,
            'search' => $search,
            'filter' => $filter, // **Passa o filtro atual de volta para o Vue**
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

        // Verifica se ainda existem alunos não vinculados
        $remainingStudents = Student::whereNull('room_id')
            ->whereNotNull('email_verified_at')
            ->count();

        // Se não houver mais alunos na página atual mas ainda existirem alunos não vinculados,
        // redireciona para a primeira página
        $currentPage = $request->input('page', 1);
        $perPage = 10;
        
        $studentsInCurrentPage = Student::whereNull('room_id')
            ->whereNotNull('email_verified_at')
            ->skip(($currentPage - 1) * $perPage)
            ->take($perPage)
            ->count();

        if ($studentsInCurrentPage <= 1 && $remainingStudents > 0) {
            return redirect()
                ->route('rooms.editrooms', [
                    'id' => $room->id,
                    'filter' => 'unlinked',
                    'page' => 1
                ])
                ->with('success', 'Aluno adicionado à sala com sucesso!');
        }

        return redirect()
            ->back()
            ->with('success', 'Aluno adicionado à sala com sucesso!');
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
    
    public function destroy($id, Request $request)
    {
        try {
            DB::transaction(function () use ($id) {
                $room = Room::findOrFail($id);
                // Desvincula todos os alunos da sala antes de deletá-la
                $room->students()->update(['room_id' => null]);
                $room->delete();
            });
        } catch (\Throwable $th) {
            Log::error('Erro ao deletar sala: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
                'room_id' => $id,
            ]);

            return redirect()->back()->withErrors([
                'error' => 'Erro ao deletar sala: ' . $th->getMessage()
            ]);
        }
        
        return redirect()->route('rooms.index')->with('success', 'Sala deletada com sucesso!');
    }
}