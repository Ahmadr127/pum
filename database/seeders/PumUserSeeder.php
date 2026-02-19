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
            ['name' => 'Indah Triyani', 'username' => 'indah.triyani', 'email' => 'indah@azra.com', 'role_name' => 'manajer_pembelian', 'unit' => 'KEUANGAN', 'nik' => '3201010101010006'],
            
            // General Manager / Other
            ['name' => 'Seni Maulida', 'username' => 'seni.maulida', 'email' => 'seni@azra.com', 'role_name' => 'manager', 'unit' => 'SEKR', 'nik' => '3201010101010007'],
            
            // Staff / Pengguna
            ['name' => 'Eka Setia', 'username' => 'eka.setia', 'email' => 'eka@azra.com', 'role_name' => 'staff', 'unit' => 'SEKR', 'nik' => '3201010101010008'],
            ['name' => 'Umar', 'username' => 'umar', 'email' => 'umar@azra.com', 'role_name' => 'staff', 'unit' => 'IT', 'nik' => '3201010101010009'],
        ];

        // 2. Remove old users
        $usersToRemove = ['dirut.pum', 'direktur.utama', 'siti.keuangan']; // Siti is now direktur.pt/correct role if needed, or just remove old username if it was 'siti.keuangan'
        User::whereIn('username', $usersToRemove)->delete();

        $this->command->info('Processing specific users...');
        foreach ($specificUsers as $data) {
            $role = Role::where('name', $data['role_name'])->first();
            $unit = null;
            if (isset($data['unit'])) {
                $unit = \App\Models\OrganizationUnit::where('code', $data['unit'])->first();
            }

            if ($role) {
                $user = User::updateOrCreate(
                    ['email' => $data['email']],
                    [
                        'username' => $data['username'],
                        'name' => $data['name'],
                        'role_id' => $role->id,
                        'organization_unit_id' => $unit?->id,
                        'password' => $password,
                        'nik' => $data['nik'] ?? null,
                    ]
                );
                
                // If user is admin and unit is IT, set as head
                if ($unit && ($data['username'] === 'admin' || $data['role_name'] === 'manager')) {
                     $unit->update(['head_id' => $user->id]);
                     $this->command->info("✓ Set {$data['username']} as head of {$unit->name}");
                }

                $this->command->info("✓ User {$data['username']} processed.");
            } else {
                $this->command->warn("! Role {$data['role_name']} not found for {$data['username']}.");
            }
        }



        $this->command->info('PUM users setup completed.');
    }
}
