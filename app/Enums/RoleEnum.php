<?php

namespace App\Enums;

enum RoleEnum: string
{
    case TANK_ADMIN = 'tank_admin';
    case OWNER = 'owner';
    case DIRECTOR = 'director';
    case TEACHER = 'teacher';
    case STUDENT = 'student';

    public function label(): string
    {
        return match ($this) {
            self::TANK_ADMIN => 'TankAdmin',
            self::OWNER => 'Owner',
            self::DIRECTOR => 'Director',
            self::TEACHER => 'Teacher',
            self::STUDENT => 'Student',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::TANK_ADMIN => 'admin.dashboard',
            self::OWNER => 'owner.dashboard',
            self::DIRECTOR => 'director.dashboard',
            self::TEACHER => 'teacher.dashboard',
            self::STUDENT => 'student.dashboard',
        };
    }
}
