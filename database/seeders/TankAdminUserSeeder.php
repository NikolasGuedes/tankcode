<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TankAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $tankAdminRole = Role::query()->firstOrCreate(
            ['name' => RoleEnum::TANK_ADMIN->value],
            ['label' => RoleEnum::TANK_ADMIN->label()],
        );

        collect([
            [
                'name' => 'Nikolas Guedes',
                'email' => 'nikolas.guedes@tankcode.com',
            ],
            [
                'name' => 'Pedro Santos',
                'email' => 'pedro.santos@tankcode.com',
            ],
            [
                'name' => 'Joao Melo',
                'email' => 'joao.melo@tankcode.comm',
            ],
        ])->each(function (array $userData) use ($tankAdminRole): void {
            User::query()->updateOrCreate(
                ['email' => $userData['email']],
                [
                    'role_id' => $tankAdminRole->id,
                    'school_id' => null,
                    'name' => $userData['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'status' => 'active',
                ],
            );
        });
    }
}
