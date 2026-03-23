<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        collect([
            ['name' => RoleEnum::TANK_ADMIN->value, 'label' => RoleEnum::TANK_ADMIN->label()],
            ['name' => RoleEnum::OWNER->value, 'label' => RoleEnum::OWNER->label()],
            ['name' => RoleEnum::DIRECTOR->value, 'label' => RoleEnum::DIRECTOR->label()],
            ['name' => RoleEnum::TEACHER->value, 'label' => RoleEnum::TEACHER->label()],
            ['name' => RoleEnum::STUDENT->value, 'label' => RoleEnum::STUDENT->label()],
        ])->each(fn (array $role) => Role::query()->updateOrCreate(
            ['name' => $role['name']],
            ['label' => $role['label']],
        ));

        $this->call([
            TankAdminUserSeeder::class,
            SchoolDemoSeeder::class,
        ]);
    }
}
