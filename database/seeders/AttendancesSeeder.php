<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Employee;

class AttendancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::all()->each(function ($employee) {
            Attendance::factory(20)->create(['emp_id' => $employee->id]);
        });
    }
}
