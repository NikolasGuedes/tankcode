<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Studentseeder extends Seeder
{
    public function run(): void
    {

        $students = [
            [
            'first_name' => 'João',
            'last_name' => 'Silva',
            'email' => 'joao@escola.com',
            'cod' => '1FV-F4C',
            'password' => bcrypt('password123')
            ],
            [
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria@escola.com',
            'cod' => '2AB-G7H',
            'password' => bcrypt('password123')
            ],
            [
            'first_name' => 'Pedro',
            'last_name' => 'Costa',
            'email' => 'pedro@escola.com',
            'cod' => '3CD-H8J',
            'password' => bcrypt('password123')
            ],
            [
            'first_name' => 'Ana',
            'last_name' => 'Oliveira',
            'email' => 'ana@escola.com',
            'cod' => '4EF-K2L',
            'password' => bcrypt('password123')
            ],
            [
            'first_name' => 'Carlos',
            'last_name' => 'Ferreira',
            'email' => 'carlos@escola.com',
            'cod' => '5GH-M3N',
            'password' => bcrypt('password123')
            ],
            [
            'first_name' => 'Luisa',
            'last_name' => 'Rodrigues',
            'email' => 'luisa@escola.com',
            'cod' => '6IJ-P4Q',
            'password' => bcrypt('password123')
            ],
            [
            'first_name' => 'Miguel',
            'last_name' => 'Almeida',
            'email' => 'miguel@escola.com',
            'cod' => '7KL-R5S',
            'password' => bcrypt('password123')
            ],
            [
            'first_name' => 'Sofia',
            'last_name' => 'Pereira',
            'email' => 'sofia@escola.com',
            'cod' => '8MN-T6U',
            'password' => bcrypt('password123')
            ],
            [
            'first_name' => 'Ricardo',
            'last_name' => 'Martins',
            'email' => 'ricardo@escola.com',
            'cod' => '9OP-V7W',
            'password' => bcrypt('password123')
            ],
            [
            'first_name' => 'Beatriz',
            'last_name' => 'Gonçalves',
            'email' => 'beatriz@escola.com',
            'cod' => '0QR-X8Y',
            'password' => bcrypt('password123')
            ],
        ];

       foreach ($students as $student) {
            \App\Models\Student::create($student);
        }
    }
}
