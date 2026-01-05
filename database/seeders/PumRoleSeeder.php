<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class PumRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Manager dengan hak approval permintaan uang muka',
            ],
            [
                'name' => 'keuangan',
                'display_name' => 'Keuangan',
                'description' => 'Bagian Keuangan yang mengelola dan menyetujui permintaan uang muka',
            ],
            [
                'name' => 'direktur',
                'display_name' => 'Direktur',
                'description' => 'Direktur Perusahaan dengan approval tertinggi',
            ],
            [
                'name' => 'staff',
                'display_name' => 'Staff',
                'description' => 'Staff yang dapat mengajukan permintaan uang muka',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                ]
            );
        }

        $this->command->info('PUM roles created successfully.');
    }
}
