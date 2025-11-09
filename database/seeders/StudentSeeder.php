<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            [
                'student_id' => '2024-001',
                'full_name' => 'Juan Dela Cruz',
                'date_of_birth' => '2002-03-15',
                'gender' => 'Male',
                'email' => 'juan.delacruz@example.com',
                'course' => 'Computer Science',
                'program' => 'Bachelor of Science',
                'year_level' => '3rd Year',
            ],
            [
                'student_id' => '2024-002',
                'full_name' => 'Maria Santos',
                'date_of_birth' => '2003-07-22',
                'gender' => 'Female',
                'email' => 'maria.santos@example.com',
                'course' => 'Information Technology',
                'program' => 'Bachelor of Science',
                'year_level' => '2nd Year',
            ],
            [
                'student_id' => '2024-003',
                'full_name' => 'Pedro Reyes',
                'date_of_birth' => '2001-11-08',
                'gender' => 'Male',
                'email' => 'pedro.reyes@example.com',
                'course' => 'Software Engineering',
                'program' => 'Bachelor of Science',
                'year_level' => '4th Year',
            ],
        ];

        foreach ($students as $student) {
            Student::create($student);
        }
    }
}
