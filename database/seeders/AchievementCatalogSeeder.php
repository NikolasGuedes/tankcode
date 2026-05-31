<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementCatalogSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            [
                'code' => 'emblem_01',
                'name' => 'Primeiro Passo',
                'description' => 'Envie sua primeira atividade para iniciar sua jornada.',
                'image_path' => 'imgs/emblemas/emblem_01.png',
                'rule_type' => 'completed_activities_gte',
                'rule_config' => ['count' => 1],
                'sort_order' => 1,
            ],
            [
                'code' => 'emblem_02',
                'name' => 'Ritmo de Aprendiz',
                'description' => 'Conclua 10 atividades e mostre consistência.',
                'image_path' => 'imgs/emblemas/emblem_02.png',
                'rule_type' => 'completed_activities_gte',
                'rule_config' => ['count' => 10],
                'sort_order' => 2,
            ],
            [
                'code' => 'emblem_03',
                'name' => 'Missões em Série',
                'description' => 'Conclua 25 atividades para provar sua dedicação.',
                'image_path' => 'imgs/emblemas/emblem_03.png',
                'rule_type' => 'completed_activities_gte',
                'rule_config' => ['count' => 25],
                'sort_order' => 3,
            ],
            [
                'code' => 'emblem_04',
                'name' => 'Veterano da Sala',
                'description' => 'Conclua 50 atividades e alcance um novo patamar.',
                'image_path' => 'imgs/emblemas/emblem_04.png',
                'rule_type' => 'completed_activities_gte',
                'rule_config' => ['count' => 50],
                'sort_order' => 4,
            ],
            [
                'code' => 'emblem_05',
                'name' => 'Gabaritou',
                'description' => 'Acerte todas as questões de uma atividade.',
                'image_path' => 'imgs/emblemas/emblem_05.png',
                'rule_type' => 'perfect_activity_once',
                'rule_config' => null,
                'sort_order' => 5,
            ],
            [
                'code' => 'emblem_06',
                'name' => 'Sequência Imbatível',
                'description' => 'Envie atividades por 7 dias consecutivos.',
                'image_path' => 'imgs/emblemas/emblem_06.png',
                'rule_type' => 'submission_streak_days',
                'rule_config' => ['days' => 7],
                'sort_order' => 6,
            ],
            [
                'code' => 'emblem_07',
                'name' => 'Perfil Completo',
                'description' => 'Complete seu perfil com foto, bio e pelo menos um link.',
                'image_path' => 'imgs/emblemas/emblem_07.png',
                'rule_type' => 'profile_completed',
                'rule_config' => null,
                'sort_order' => 7,
            ],
            [
                'code' => 'emblem_08',
                'name' => 'Lenda dos Pontos',
                'description' => 'Acumule 1000 pontos ou mais ao longo da jornada.',
                'image_path' => 'imgs/emblemas/emblem_08.png',
                'rule_type' => 'points_gte',
                'rule_config' => ['points' => 1000],
                'sort_order' => 8,
            ],
        ])->each(function (array $achievement): void {
            Achievement::query()->updateOrCreate(
                ['code' => $achievement['code']],
                [
                    'name' => $achievement['name'],
                    'description' => $achievement['description'],
                    'image_path' => $achievement['image_path'],
                    'rule_type' => $achievement['rule_type'],
                    'rule_config' => $achievement['rule_config'],
                    'sort_order' => $achievement['sort_order'],
                    'is_active' => true,
                ],
            );
        });
    }
}
