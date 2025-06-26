<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Change to App\Models\Admin if using a separate model
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         // Check if the admin already exists
        if (!User::where('employee_number', '020202')->exists()) {
            User::create([
                'lastname' => 'Lariosa',
                'firstname' => 'Jobelle',
                'middlename' => 'Mercado',
                'ext_name' => '',
                'username' => 'Jobelle Lariosa',
                'password' => Hash::make('lariosajobellemercado'), // Default password
                'role' => '1', // Assuming '1' is the role for admin
                'employee_number' => '020202',
            ]);
        }
    }
}
