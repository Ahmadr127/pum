<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PumPegawaiPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create permissions
        $permissions = [
            ['name' => 'create_pum', 'display_name' => 'Buat PUM', 'description' => 'Membuat dan melihat pengajuan PUM sendiri'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
        }

        // 2. Assign to roles
        // Staff/Pegawai
        $staffRole = Role::where('name', 'staff')->first();
        if ($staffRole) {
            $permission = Permission::where('name', 'create_pum')->first();
            if (!$staffRole->permissions->contains($permission->id)) {
                $staffRole->permissions()->attach($permission);
                $this->command->info("Granted 'create_pum' to Staff");
            }
        }

        // Admin
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
             $permission = Permission::where('name', 'create_pum')->first();
             if (!$adminRole->permissions->contains($permission->id)) {
                $adminRole->permissions()->attach($permission);
                $this->command->info("Granted 'create_pum' to Admin");
             }
        }
    }
}
