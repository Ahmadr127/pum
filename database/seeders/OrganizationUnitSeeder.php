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

        // Manager IT
        // $managerIT = $this->createUser('Budi Manager IT', 'budi.it', 'manager.it@hospital.com', $managerRole->id, $it->id);
        // $it->update(['head_id' => $managerIT->id]);

        // Staff IT (1 user)
        // $this->createUser('Citra Staff IT', 'citra.it', 'citra.it@hospital.com', $staffRole->id, $it->id);

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

        // Get keuangan role
        $keuanganRole = Role::firstOrCreate(
            ['name' => 'manajer_keuangan'],
            ['display_name' => 'Manajer Keuangan', 'description' => 'Bagian Keuangan']
        );

        // Manager Keuangan
        $managerKeuangan = $this->createUser('Ria Fajarrohmi', 'ria.fajarrohmi', 'ria.fajarrohmi@hospital.com', $keuanganRole->id, $keuangan->id);
        $keuangan->update(['head_id' => $managerKeuangan->id]);

        // Staff Keuangan (1 user)
        $this->createUser('Rina Staff Keuangan', 'rina.keuangan', 'rina.keuangan@hospital.com', $staffRole->id, $keuangan->id);

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

        // Manager Sekretaris
        // $managerSekretaris = $this->createUser('Erna Manager Sekretaris', 'erna.sekretaris', 'manager.sekretaris@hospital.com', $managerRole->id, $sekretaris->id);
        // $sekretaris->update(['head_id' => $managerSekretaris->id]);

        // Staff Sekretaris (1 user)
        // $this->createUser('Fitri Staff Sekretaris', 'fitri.sekretaris', 'fitri.sekretaris@hospital.com', $staffRole->id, $sekretaris->id);

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

        // Manager Keperawatan
        // $managerKeperawatan = $this->createUser('Hana Manager Keperawatan', 'hana.keperawatan', 'manager.keperawatan@hospital.com', $managerRole->id, $keperawatan->id);
        // $keperawatan->update(['head_id' => $managerKeperawatan->id]);

        // Staff Keperawatan (1 user)
        // $this->createUser('Indah Staff Keperawatan', 'indah.keperawatan', 'indah.keperawatan@hospital.com', $staffRole->id, $keperawatan->id);

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

        // Manager Rawat Inap
        // $managerRanap = $this->createUser('Kiki Manager Rawat Inap', 'kiki.ranap', 'manager.ranap@hospital.com', $managerRole->id, $rawatInap->id);
        // $rawatInap->update(['head_id' => $managerRanap->id]);

        // Staff Rawat Inap (1 user)
        // $this->createUser('Lina Perawat Ranap', 'lina.ranap', 'lina.ranap@hospital.com', $staffRole->id, $rawatInap->id);

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

        // Manager IGD
        // $managerIgd = $this->createUser('Nana Manager IGD', 'nana.igd', 'manager.igd@hospital.com', $managerRole->id, $igd->id);
        // $igd->update(['head_id' => $managerIgd->id]);

        // Staff IGD (1 user)
        // $this->createUser('Oscar Perawat IGD', 'oscar.igd', 'oscar.igd@hospital.com', $staffRole->id, $igd->id);
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
            'password' => Hash::make('password'),
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
            'password' => Hash::make('password'),
            'role_id' => $roleId,
            'organization_unit_id' => $unit->id,
        ]);

        // Set user as head
        $unit->update(['head_id' => $user->id]);

        return ['user' => $user, 'unit' => $unit];
    }
}
