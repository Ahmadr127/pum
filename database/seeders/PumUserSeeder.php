<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PumUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');
        
        // 1. Specific Users
        $specificUsers = [
            // PT Board / High Level
            ['name' => 'Budi Manager PT', 'username' => 'manager.pt', 'email' => 'manager.pt@example.com', 'role_name' => 'manager_pt', 'unit' => 'DIRUT', 'nik' => '3201010101010001'],
            ['name' => 'Siti Direktur PT', 'username' => 'direktur.pt', 'email' => 'direktur.pt@example.com', 'role_name' => 'direktur_pt', 'unit' => 'DIRUT', 'nik' => '3201010101010002'],
            
            // RS Executives
            ['name' => 'dr. Irma Rismayanti, MM', 'username' => 'irma.rismayanti', 'email' => 'irma@azra.com', 'role_name' => 'hospital_director', 'unit' => 'DIRUT', 'nik' => '3201010101010003'],
            
            // Admin
            ['name' => 'Muhamad Miftahudin', 'username' => 'admin', 'email' => 'admin@azra.com', 'role_name' => 'admin', 'unit' => 'IT', 'nik' => '3201010101010004'],
            
            // Keuangan Team
            ['name' => 'Ria Fajarrohmi', 'username' => 'ria.fajarrohmi', 'email' => 'ria@azra.com', 'role_name' => 'manajer_keuangan', 'unit' => 'KEUANGAN', 'nik' => '3201010101010005'],
            
            // General Manager / Other
            ['name' => 'Seni Maulida', 'username' => 'seni.maulida', 'email' => 'seni@azra.com', 'role_name' => 'manager', 'unit' => 'SEKR', 'nik' => '3201010101010007'],
            
            // Staff / Pengguna
            ['name' => 'Eka Setia', 'username' => 'eka.setia', 'email' => 'eka@azra.com', 'role_name' => 'staff', 'unit' => 'SEKR', 'nik' => '3201010101010008'],
            ['name' => 'Umar', 'username' => 'umar', 'email' => 'umar@azra.com', 'role_name' => 'staff', 'unit' => 'IT', 'nik' => '3201010101010009'],
        ];

        // NOTE: User creation is now handled by OrganizationUsersSeeder
        $this->command->info('User creation is now handled by OrganizationUsersSeeder');
        $this->command->info('PUM users setup completed.');
    }
}
