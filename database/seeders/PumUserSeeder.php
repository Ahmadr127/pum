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
            ['name' => 'Budi Manager PT', 'username' => 'manager.pt', 'email' => 'manager.pt@example.com', 'role_name' => 'manager_pt'],
            ['name' => 'Siti Direktur PT', 'username' => 'direktur.pt', 'email' => 'direktur.pt@example.com', 'role_name' => 'direktur_pt'],
            ['name' => 'Muhamad Miftahudin', 'username' => 'admin', 'email' => 'admin@azra.com', 'role_name' => 'admin'],
            ['name' => 'dr. Irma Rismayanti, MM', 'username' => 'irma.rismayanti', 'email' => 'irma@azra.com', 'role_name' => 'direktur_rs'],
            ['name' => 'Ria Fajarrohmi', 'username' => 'ria.fajarrohmi', 'email' => 'ria@azra.com', 'role_name' => 'manajer_keuangan'],
            ['name' => 'Indah Triyani', 'username' => 'indah.triyani', 'email' => 'indah@azra.com', 'role_name' => 'purchasing'],
            ['name' => 'Seni Maulida', 'username' => 'seni.maulida', 'email' => 'seni@azra.com', 'role_name' => 'manager'],
            ['name' => 'Eka Setia', 'username' => 'eka.setia', 'email' => 'eka@azra.com', 'role_name' => 'pengguna'],
            ['name' => 'Umar', 'username' => 'umar', 'email' => 'umar@azra.com', 'role_name' => 'pengguna'],
        ];

        // 2. Workflow Users (if not already in specific list)
        $workflowUsers = [
            'koordinator' => ['name' => 'Koordinator PUM', 'username' => 'koordinator.pum', 'unit' => 'KEUANGAN'],
            'supervisor' => ['name' => 'Supervisor PUM', 'username' => 'supervisor.pum', 'unit' => 'KEUANGAN'],
            'kepala_unit' => ['name' => 'Kepala Unit PUM', 'username' => 'kepala.unit.pum', 'unit' => 'DIRUT'],
            'manajer_pembelian' => ['name' => 'Manajer Pembelian PUM', 'username' => 'manajer.pembelian', 'unit' => 'DIRUT'],
            'direktur_operasional' => ['name' => 'Direktur Operasional PUM', 'username' => 'direktur.operasional', 'unit' => 'DIRUT'],
            'spv_1' => ['name' => 'SPV 1 PUM', 'username' => 'spv1.pum', 'unit' => 'KEUANGAN'],
            'direktur_it' => ['name' => 'Direktur IT PUM', 'username' => 'direktur.it', 'unit' => 'IT'],
            'manajer_it' => ['name' => 'Manajer IT PUM', 'username' => 'manajer.it.pum', 'unit' => 'IT'],
            'direktur_keuangan' => ['name' => 'Direktur Keuangan PUM', 'username' => 'direktur.keuangan', 'unit' => 'KEUANGAN'],
            'direktur_utama' => ['name' => 'Direktur Utama PUM', 'username' => 'dirut.pum', 'unit' => 'DIRUT'],
        ];

        $this->command->info('Processing specific users...');
        foreach ($specificUsers as $data) {
            $role = Role::where('name', $data['role_name'])->first();
            if ($role) {
                User::updateOrCreate(
                    ['username' => $data['username']],
                    [
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'role_id' => $role->id,
                        'password' => $password, // Ensuring we can login
                    ]
                );
                $this->command->info("✓ User {$data['username']} processed.");
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
