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
        // Get roles
        $direkturRole = Role::where('name', 'direktur')->first();

        // Get organization units
        $direkturUnit = \App\Models\OrganizationUnit::where('code', 'DIRUT')->first();

        // Check if direktur user already exists from OrganizationUnitSeeder
        $existingDirektur = User::where('username', 'direktur.utama')->first();
        
        if ($existingDirektur) {
            // Update existing user to have direktur role
            $existingDirektur->update([
                'role_id' => $direkturRole?->id,
            ]);
            $this->command->info('✓ User Direktur Utama updated with direktur role');
        } else {
            // Create new direktur user if not exists
            User::firstOrCreate(
                ['email' => 'direktur@pum.test'],
                [
                    'name' => 'Dr. Ahmad Direktur',
                    'username' => 'direktur.pum',
                    'password' => Hash::make('password'),
                    'role_id' => $direkturRole?->id,
                    'organization_unit_id' => $direkturUnit?->id,
                ]
            );
            $this->command->info('✓ User Direktur created');
        }

        $this->command->info('PUM users setup completed.');
        $this->command->info('Default password for all users: password');
        $this->command->info('✓ User Manager dan Staff PUM dihapus (tidak digunakan)');
        $this->command->info('✓ User Keuangan ada di Departemen Keuangan (siti.keuangan)');
    }
}
