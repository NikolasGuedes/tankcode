<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $user = request()->user();
        $routeName = request()->route()?->getName();

        [$headline, $description] = match ($routeName) {
            'owner.dashboard' => ['Gestao da Escola', 'Acompanhe a estrutura da sua escola, pontos de ensino vinculados e equipes distribuidas por unidade.'],
            'director.dashboard' => ['Portal da Diretoria', 'Visualize a operacao da unidade e os usuarios sob sua responsabilidade.'],
            'teacher.dashboard' => ['Portal do Professor', 'Seu espaco de trabalho para acompanhar turmas, unidades e comunicacoes.'],
            'student.dashboard' => ['Portal do Aluno', 'Acesse sua jornada academica a partir do login unificado da plataforma.'],
            'admin.dashboard' => ['Administracao TankCode', 'Gerencie o embarque de escolas, pontos de ensino e usuarios do sistema.'],
            default => ['Portal', 'Acesse sua area de trabalho de acordo com seu papel no sistema.'],
        };

        return Inertia::render('portal/Index', [
            'headline' => $headline,
            'description' => $description,
            'role' => match ($user?->role?->name) {
                RoleEnum::TANK_ADMIN => RoleEnum::TANK_ADMIN->label(),
                RoleEnum::OWNER => RoleEnum::OWNER->label(),
                RoleEnum::DIRECTOR => RoleEnum::DIRECTOR->label(),
                RoleEnum::TEACHER => RoleEnum::TEACHER->label(),
                RoleEnum::STUDENT => RoleEnum::STUDENT->label(),
                default => $user?->role?->label,
            },
        ]);
    }
}
