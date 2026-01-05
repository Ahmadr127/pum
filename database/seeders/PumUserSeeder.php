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
        $managerRole = Role::where('name', 'manager')->first();
        $keuanganRole = Role::where('name', 'keuangan')->first();
        $direkturRole = Role::where('name', 'direktur')->first();
        $staffRole = Role::where('name', 'staff')->first();

        $users = [
            [
                'name' => 'Manager PUM',
                'email' => 'manager@pum.test',
                'username' => 'manager.pum',
                'password' => Hash::make('password'),
                'role_id' => $managerRole?->id,
            ],
            [
                'name' => 'Keuangan PUM',
                'email' => 'keuangan@pum.test',
                'username' => 'keuangan.pum',
                'password' => Hash::make('password'),
                'role_id' => $keuanganRole?->id,
            ],
            [
                'name' => 'Direktur PUM',
                'email' => 'direktur@pum.test',
                'username' => 'direktur.pum',
                'password' => Hash::make('password'),
                'role_id' => $direkturRole?->id,
            ],
            [
                'name' => 'Staff PUM',
                'email' => 'staff@pum.test',
                'username' => 'staff.pum',
                'password' => Hash::make('password'),
                'role_id' => $staffRole?->id,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('PUM users created successfully.');
        $this->command->info('Default password for all users: password');
    }
}
