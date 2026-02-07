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
        
        // 1. Specific Users from provided text
        $specificUsers = [
            // PT Board / High Level
            ['name' => 'Budi Manager PT', 'username' => 'manager.pt', 'email' => 'manager.pt@example.com', 'role_name' => 'manager', 'unit' => 'DIRUT'], // Assumed DIRUT/Holding
            ['name' => 'Siti Direktur PT', 'username' => 'direktur.pt', 'email' => 'direktur.pt@example.com', 'role_name' => 'direktur_utama', 'unit' => 'DIRUT'],
            
            // RS Executives
            ['name' => 'dr. Irma Rismayanti, MM', 'username' => 'irma.rismayanti', 'email' => 'irma@azra.com', 'role_name' => 'direktur_utama', 'unit' => 'DIRUT'], // Direktur RS -> Direktur Utama
            
            // Admin
            ['name' => 'Muhamad Miftahudin', 'username' => 'admin', 'email' => 'admin@azra.com', 'role_name' => 'admin', 'unit' => 'IT'],
            
            // Keuangan Team
            ['name' => 'Ria Fajarrohmi', 'username' => 'ria.fajarrohmi', 'email' => 'ria@azra.com', 'role_name' => 'manajer_keuangan', 'unit' => 'KEUANGAN'],
            ['name' => 'Indah Triyani', 'username' => 'indah.triyani', 'email' => 'indah@azra.com', 'role_name' => 'manajer_pembelian', 'unit' => 'KEUANGAN'], // Purchasing -> Manajer Pembelian? Or Staff?
            
            // General Manager / Other
            ['name' => 'Seni Maulida', 'username' => 'seni.maulida', 'email' => 'seni@azra.com', 'role_name' => 'manager', 'unit' => 'SEKR'], // Assigning to SEKR as filler/manager
            
            // Staff / Pengguna
            ['name' => 'Eka Setia', 'username' => 'eka.setia', 'email' => 'eka@azra.com', 'role_name' => 'staff', 'unit' => 'SEKR'],
            ['name' => 'Umar', 'username' => 'umar', 'email' => 'umar@azra.com', 'role_name' => 'staff', 'unit' => 'IT'],
        ];

        // 2. Workflow Users (if not already in specific list)
        $workflowUsers = [

            'direktur_utama' => ['name' => 'Direktur Utama PUM', 'username' => 'dirut.pum', 'unit' => 'DIRUT'],
        ];

        $this->command->info('Processing specific users...');
        foreach ($specificUsers as $data) {
            $role = Role::where('name', $data['role_name'])->first();
            $unit = null;
            if (isset($data['unit'])) {
                $unit = \App\Models\OrganizationUnit::where('code', $data['unit'])->first();
            }

            if ($role) {
                $user = User::updateOrCreate(
                    ['username' => $data['username']],
                    [
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'role_id' => $role->id,
                        'organization_unit_id' => $unit?->id,
                        'password' => $password,
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

        $this->command->info('Processing workflow users...');
        foreach ($workflowUsers as $roleName => $data) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $unit = \App\Models\OrganizationUnit::where('code', $data['unit'])->first();
                User::updateOrCreate(
                    ['username' => $data['username']],
                    [
                        'name' => $data['name'],
                        'email' => "{$data['username']}@pum.test",
                        'role_id' => $role->id,
                        'organization_unit_id' => $unit?->id,
                        'password' => $password,
                    ]
                );
                $this->command->info("✓ Workflow user {$data['username']} processed.");
            }
        }

        $this->command->info('PUM users setup completed.');
    }
}
