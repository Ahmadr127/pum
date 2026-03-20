<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganizationType;
use App\Models\OrganizationUnit;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrganizationUnitSeeder extends Seeder
{
    public function run(): void
    {
        // Get types
        $holdingType = OrganizationType::where('name', 'holding')->first();
        $hospitalType = OrganizationType::where('name', 'hospital')->first();
        $directorateType = OrganizationType::where('name', 'directorate')->first();
        $departmentType = OrganizationType::where('name', 'department')->first();
        $unitType = OrganizationType::where('name', 'unit')->first();

        // Get or create manager role
        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            ['display_name' => 'Manager', 'description' => 'Manager unit organisasi']
        );
        
        $staffRole = Role::firstOrCreate(
            ['name' => 'staff'],
            ['display_name' => 'Staff', 'description' => 'Staff umum']
        );

        // ========================================
        // 1. DIREKTUR UTAMA (Top Level)
        // ========================================
        // Get or create hospital_director role
        $hospitalDirectorRole = Role::firstOrCreate(
            ['name' => 'hospital_director'],
            ['display_name' => 'Hospital Director', 'description' => 'Direktur Rumah Sakit']
        );

        // ========================================
        // 1. DIREKTUR UTAMA (Top Level)
        // ========================================
        $direkturUtama = $this->createUserWithUnit(
            'dr. Irma Rismayanti, MM',
            'irma.rismayanti',
            'irma@azra.com',
            'Direktur Utama',
            'DIRUT',
            $holdingType->id,
            null,
            'Direktur Utama Rumah Sakit',
            $hospitalDirectorRole->id
        );

        // ========================================
        // 2. DEPARTEMEN IT (langsung dibawah Direktur Utama)
        // ========================================
        $it = OrganizationUnit::create([
            'name' => 'Departemen IT',
            'code' => 'IT',
            'type_id' => $departmentType->id,
            'parent_id' => $direkturUtama['unit']->id,
            'description' => 'Teknologi Informasi',
            'is_active' => true,
        ]);

        // NOTE: User creation is now handled by OrganizationUsersSeeder

        // ========================================
        // 3. DEPARTEMEN KEUANGAN (langsung dibawah Direktur Utama)
        // ========================================
        $keuangan = OrganizationUnit::create([
            'name' => 'Departemen Keuangan',
            'code' => 'KEUANGAN',
            'type_id' => $departmentType->id,
            'parent_id' => $direkturUtama['unit']->id,
            'description' => 'Departemen Keuangan dan Akuntansi',
            'is_active' => true,
        ]);

        // NOTE: User creation is now handled by OrganizationUsersSeeder

        // ========================================
        // 4. DEPARTEMEN SEKRETARIS (langsung dibawah Direktur Utama)
        // ========================================
        $sekretaris = OrganizationUnit::create([
            'name' => 'Departemen Sekretaris',
            'code' => 'SEKR',
            'type_id' => $departmentType->id,
            'parent_id' => $direkturUtama['unit']->id,
            'description' => 'Departemen Sekretaris Direktur',
            'is_active' => true,
        ]);

        // NOTE: User creation is now handled by OrganizationUsersSeeder

        // ========================================
        // 5. DEPARTEMEN KEPERAWATAN (langsung dibawah Direktur Utama)
        // ========================================
        $keperawatan = OrganizationUnit::create([
            'name' => 'Departemen Keperawatan',
            'code' => 'PERAWAT',
            'type_id' => $departmentType->id,
            'parent_id' => $direkturUtama['unit']->id,
            'description' => 'Departemen Keperawatan',
            'is_active' => true,
        ]);

        // NOTE: User creation is now handled by OrganizationUsersSeeder

        // ========================================
        // 6. DEPARTEMEN RAWAT INAP (langsung dibawah Direktur Utama - setingkat departemen)
        // ========================================
        $rawatInap = OrganizationUnit::create([
            'name' => 'Departemen Rawat Inap',
            'code' => 'RANAP',
            'type_id' => $departmentType->id,
            'parent_id' => $direkturUtama['unit']->id,
            'description' => 'Departemen Rawat Inap',
            'is_active' => true,
        ]);

        // NOTE: User creation is now handled by OrganizationUsersSeeder

        // ========================================
        // 7. DEPARTEMEN IGD (langsung dibawah Direktur Utama - setingkat departemen)
        // ========================================
        $igd = OrganizationUnit::create([
            'name' => 'Departemen IGD',
            'code' => 'IGD',
            'type_id' => $departmentType->id,
            'parent_id' => $direkturUtama['unit']->id,
            'description' => 'Departemen Instalasi Gawat Darurat',
            'is_active' => true,
        ]);

        // NOTE: User creation is now handled by OrganizationUsersSeeder
    }

    /**
     * Helper function to create a user
     */
    private function createUser(string $name, string $username, string $email, int $roleId, int $unitId): User
    {
        return User::create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => Hash::make('rsazra'),
            'role_id' => $roleId,
            'organization_unit_id' => $unitId,
        ]);
    }

    /**
     * Helper function to create user and unit together
     */
    private function createUserWithUnit(
        string $userName,
        string $username,
        string $email,
        string $unitName,
        string $unitCode,
        int $typeId,
        ?int $parentId,
        string $description,
        int $roleId
    ): array {
        // Create unit first
        $unit = OrganizationUnit::create([
            'name' => $unitName,
            'code' => $unitCode,
            'type_id' => $typeId,
            'parent_id' => $parentId,
            'description' => $description,
            'is_active' => true,
        ]);

        // Create user
        $user = User::create([
            'name' => $userName,
            'username' => $username,
            'email' => $email,
            'password' => Hash::make('rsazra'),
            'role_id' => $roleId,
            'organization_unit_id' => $unit->id,
        ]);

        // Set user as head
        $unit->update(['head_id' => $user->id]);

        return ['user' => $user, 'unit' => $unit];
    }
}
