# Analisis dan Perbaikan Seeder PUM

## 📋 Masalah yang Ditemukan

### 1. **Role Keuangan Tanpa User dan Departemen**
- ✅ Role `keuangan` sudah dibuat di `PumRoleSeeder.php`
- ✅ Role `keuangan` digunakan dalam workflow approval di `PumWorkflowSeeder.php`
- ❌ **MASALAH**: User dengan role keuangan tidak memiliki `organization_unit_id`
- ❌ **MASALAH**: Tidak ada Departemen Keuangan di `OrganizationUnitSeeder.php`

### 2. **Struktur Unit Tidak Konsisten**
- ❌ **MASALAH**: Unit-unit organisasi menggunakan type yang berbeda-beda
  - Beberapa menggunakan `departmentType` (level 4)
  - Beberapa menggunakan `unitType` (level 5)
- ❌ **MASALAH**: Hierarki tidak konsisten (ada unit yang menjadi child dari departemen)

---

## ✅ Perbaikan yang Dilakukan

### 1. **OrganizationUnitSeeder.php**

#### Perubahan Struktur:
```
SEBELUM:
├── Direktur Utama (holding)
    ├── Departemen SIRS (department)
    ├── Sekretaris (department)
    └── Departemen Keperawatan (department)
        ├── Unit Rawat Inap (unit) ← Hierarki lebih dalam
        └── Unit IGD (unit) ← Hierarki lebih dalam

SESUDAH:
├── Direktur Utama (holding)
    ├── Departemen SIRS (department)
    ├── Departemen Keuangan (department) ← BARU DITAMBAHKAN
    ├── Departemen Sekretaris (department)
    ├── Departemen Keperawatan (department)
    ├── Departemen Rawat Inap (department) ← Dipindah ke level yang sama
    └── Departemen IGD (department) ← Dipindah ke level yang sama
```

#### Detail Perubahan:

**A. Menambahkan Departemen Keuangan (BARU)**
```php
// Departemen Keuangan
$keuangan = OrganizationUnit::create([
    'name' => 'Departemen Keuangan',
    'code' => 'KEUANGAN',
    'type_id' => $departmentType->id,
    'parent_id' => $direkturUtama['unit']->id,
    'description' => 'Departemen Keuangan dan Akuntansi',
    'is_active' => true,
]);

// Manager Keuangan (dengan role keuangan)
$managerKeuangan = $this->createUser(
    'Siti Manager Keuangan', 
    'siti.keuangan', 
    'manager.keuangan@hospital.com', 
    $keuanganRole->id, 
    $keuangan->id
);

// Staff Keuangan (2 users)
- Rina Staff Keuangan (rina.keuangan)
- Tono Staff Keuangan (tono.keuangan)
```

**B. Mengubah Semua Unit Menjadi Setingkat Departemen**
- ✅ Rawat Inap: `unitType` → `departmentType`, parent: `keperawatan` → `direkturUtama`
- ✅ IGD: `unitType` → `departmentType`, parent: `keperawatan` → `direkturUtama`
- ✅ Semua unit sekarang langsung di bawah Direktur Utama dengan level yang sama

**C. Update Nama Unit**
- ✅ "Sekretaris" → "Departemen Sekretaris"
- ✅ "Unit Rawat Inap" → "Departemen Rawat Inap"
- ✅ "Unit IGD" → "Departemen IGD"

---

### 2. **PumUserSeeder.php**

#### Menghubungkan User dengan Organization Unit:

```php
// Mendapatkan organization units
$keuanganUnit = \App\Models\OrganizationUnit::where('code', 'KEUANGAN')->first();
$direkturUnit = \App\Models\OrganizationUnit::where('code', 'DIRUT')->first();

// User Keuangan PUM
[
    'name' => 'Keuangan PUM',
    'email' => 'keuangan@pum.test',
    'username' => 'keuangan.pum',
    'password' => Hash::make('password'),
    'role_id' => $keuanganRole?->id,
    'organization_unit_id' => $keuanganUnit?->id, // ← DITAMBAHKAN
]

// User Direktur PUM
[
    'name' => 'Direktur PUM',
    'email' => 'direktur@pum.test',
    'username' => 'direktur.pum',
    'password' => Hash::make('password'),
    'role_id' => $direkturRole?->id,
    'organization_unit_id' => $direkturUnit?->id, // ← DITAMBAHKAN
]
```

---

## 📊 Ringkasan Perubahan

### Departemen yang Ditambahkan:
1. **Departemen Keuangan** (KEUANGAN)
   - Manager: Siti Manager Keuangan (siti.keuangan)
   - Staff: Rina Staff Keuangan, Tono Staff Keuangan

### Unit yang Diubah Levelnya:
1. **Departemen Rawat Inap** (RANAP)
   - Sebelum: Unit level 5, child dari Keperawatan
   - Sesudah: Departemen level 4, langsung di bawah Direktur Utama

2. **Departemen IGD** (IGD)
   - Sebelum: Unit level 5, child dari Keperawatan
   - Sesudah: Departemen level 4, langsung di bawah Direktur Utama

### User yang Diupdate:
1. **Keuangan PUM** (keuangan@pum.test)
   - Sebelum: `organization_unit_id` = null
   - Sesudah: `organization_unit_id` = Departemen Keuangan

2. **Direktur PUM** (direktur@pum.test)
   - Sebelum: `organization_unit_id` = null
   - Sesudah: `organization_unit_id` = Direktur Utama

---

## 🔄 Cara Menjalankan Seeder

### Urutan yang Benar:
```bash
# 1. Reset database (opsional, jika ingin fresh)
php artisan migrate:fresh

# 2. Jalankan seeder dalam urutan yang benar
php artisan db:seed --class=OrganizationTypeSeeder
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=PumRoleSeeder
php artisan db:seed --class=OrganizationUnitSeeder
php artisan db:seed --class=PumUserSeeder
php artisan db:seed --class=PumWorkflowSeeder

# ATAU jalankan semua sekaligus (jika DatabaseSeeder sudah dikonfigurasi)
php artisan db:seed
```

---

## ✅ Verifikasi

### Cek Departemen Keuangan:
```sql
SELECT * FROM organization_units WHERE code = 'KEUANGAN';
```

### Cek User Keuangan:
```sql
SELECT u.name, u.email, u.username, ou.name as department, r.display_name as role
FROM users u
LEFT JOIN organization_units ou ON u.organization_unit_id = ou.id
LEFT JOIN roles r ON u.role_id = r.id
WHERE u.email = 'keuangan@pum.test';
```

### Cek Workflow Approval:
```sql
SELECT w.name, s.order, s.name as step_name, r.display_name as role
FROM pum_approval_workflows w
JOIN pum_approval_steps s ON w.id = s.workflow_id
JOIN roles r ON s.role_id = r.id
WHERE w.is_default = 1
ORDER BY s.order;
```

Expected result:
```
1. Approval Manager (Manager)
2. Approval Keuangan (Keuangan) ← Sekarang ada departemennya
3. Approval Direktur (Direktur)
```

---

## 📝 Catatan Penting

1. **Konsistensi Level**: Semua departemen sekarang berada di level yang sama (level 4 - department)
2. **Hierarki Flat**: Struktur organisasi sekarang lebih flat, semua departemen langsung di bawah Direktur Utama
3. **User-Unit Relationship**: User keuangan dan direktur sekarang memiliki departemen yang jelas
4. **Workflow Compatibility**: Workflow approval tetap berfungsi dengan baik karena menggunakan role, bukan unit

---

## 🎯 Kesimpulan

✅ **Masalah Terselesaikan:**
- Role keuangan sekarang memiliki departemen dan user yang terhubung
- Semua unit sekarang setingkat sebagai departemen
- Struktur organisasi lebih konsisten dan mudah dipahami
- Workflow approval tetap berfungsi dengan baik
