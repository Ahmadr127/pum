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
        $this->command->info('🟢 Starting PUM User Seeder...');
        $this->command->newLine();

        // Create dummy Manager PT user for PUM
        $this->createDummyManagerPtUser();

        $this->command->newLine();
        $this->command->info('✅ PUM users setup completed.');
    }

    private function createDummyManagerPtUser(): void
    {
        $this->command->info('📝 Creating Dummy Manager PT User...');

        // Get or create manager_pt role
        $managerPtRole = Role::firstOrCreate(
            ['name' => 'manager_pt'],
            [
                'display_name' => 'Manager PT',
                'description' => 'Manager PT - Approver dan Releaser'
            ]
        );

        // Create dummy Manager PT user
        $user = User::firstOrCreate(
            ['username' => 'manager.pt'],
            [
                'nik' => '32010199990001',
                'name' => 'Budi Manager PT',
                'email' => 'manager.pt@azra.com',
                'password' => Hash::make('rsazra'),
                'role_id' => $managerPtRole->id,
            ]
        );

        $this->command->info("  ✓ Dummy User: {$user->name} (manager_pt)");
        $this->command->info('');
        $this->command->info('💡 Dummy User Credentials:');
        $this->command->info('   Username: manager.pt');
        $this->command->info('   Password: rsazra');
    }
}
