<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class AssignDashboardPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure view_dashboard permission exists
        $dashboardPerm = Permission::firstOrCreate(
            ['name' => 'view_dashboard'],
            ['display_name' => 'Lihat Dashboard', 'description' => 'Melihat halaman dashboard']
        );

        // Assign to ALL roles
        $roles = Role::all();
        foreach ($roles as $role) {
            $role->permissions()->syncWithoutDetaching([$dashboardPerm->id]);
            echo "Dashboard permission assigned to: {$role->name}\n";
        }
        
        echo "\nDone! All roles now have view_dashboard permission.\n";
    }
}
