<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'System Admin',
            'first_name' => 'System',
            'last_name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role' => 'Admin',
            'user_type' => 1,
            'employee_id' => 'EMP0001',
            'username' => 'admin',
            'password' => Hash::make('123456'),
            'company_id' => 1,
            'location_id' => 1,
            'unit_id' => 1,
            'department_id' => 1,
            'designation_id' => 'Administrator',
            'mobile' => '9876543210',
            'created_by' => 1,
            'status' => 1,
            'trash' => 'NO',
        ]);
         User::factory()->count(2)->create();
    }
}
