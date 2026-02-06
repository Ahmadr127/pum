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
                'name' => 'koordinator',
                'display_name' => 'Koordinator',
                'description' => 'Koordinator yang mengelola approval',
            ],
            [
                'name' => 'supervisor',
                'display_name' => 'Supervisor',
                'description' => 'Supervisor yang mengelola approval',
            ],
            [
                'name' => 'kepala_unit',
                'display_name' => 'Kepala Unit',
                'description' => 'Kepala Unit yang mengelola approval',
            ],
            [
                'name' => 'manajer_pembelian',
                'display_name' => 'Manajer Pembelian',
                'description' => 'Manajer Pembelian yang mengelola approval',
            ],
            [
                'name' => 'direktur_operasional',
                'display_name' => 'Direktur Operasional',
                'description' => 'Direktur Operasional yang mengelola approval',
            ],
            [
                'name' => 'spv_1',
                'display_name' => 'SPV 1',
                'description' => 'Supervisor 1 yang mengelola approval',
            ],
            [
                'name' => 'manajer_keuangan',
                'display_name' => 'Manajer Keuangan',
                'description' => 'Manajer Keuangan yang mengelola approval',
            ],
            [
                'name' => 'direktur_it',
                'display_name' => 'Direktur IT',
                'description' => 'Direktur IT yang mengelola approval',
            ],
            [
                'name' => 'manajer_it',
                'display_name' => 'Manajer IT',
                'description' => 'Manajer IT yang mengelola approval',
            ],
            [
                'name' => 'direktur_keuangan',
                'display_name' => 'Direktur Keuangan',
                'description' => 'Direktur Keuangan yang mengelola approval',
            ],
            [
                'name' => 'direktur_utama',
                'display_name' => 'Direktur Utama',
                'description' => 'Direktur Utama dengan approval tertinggi',
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
