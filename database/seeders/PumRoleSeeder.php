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
                'name' => 'manajer_pembelian',
                'display_name' => 'Manajer Pembelian',
                'description' => 'Manajer Pembelian yang mengelola approval',
            ],
            [
                'name' => 'hospital_director',
                'display_name' => 'Hospital Director',
                'description' => 'Direktur Rumah Sakit',
            ],
            [
                'name' => 'manager_pt',
                'display_name' => 'Manager PT',
                'description' => 'Manager PT',
            ],
            [
                'name' => 'manajer_keuangan',
                'display_name' => 'Manajer Keuangan',
                'description' => 'Manajer Keuangan yang mengelola approval',
            ],
            [
                'name' => 'direktur_pt',
                'display_name' => 'Direktur PT',
                'description' => 'Direktur PT',
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
