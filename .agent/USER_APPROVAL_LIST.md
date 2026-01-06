# 📋 Daftar User untuk Approval Workflow PUM

## 🔐 User yang Terlibat dalam Approval Workflow

Workflow approval PUM memiliki 3 tingkat persetujuan:
**Manager → Keuangan → Direktur**

---

## 👥 Daftar User Approval

### 1. **Manager** (Approval Level 1)
| Field | Value |
|-------|-------|
| **Nama** | Manager PUM |
| **Email** | manager@pum.test |
| **Username** | manager.pum |
| **Password** | password |
| **Role** | Manager |
| **Departemen** | - (Manager umum) |
| **Permissions** | • view_dashboard<br>• manage_pum<br>• approve_pum |

**Login:**
```
Username: manager.pum
Password: password
```

---

### 2. **Keuangan** (Approval Level 2)
| Field | Value |
|-------|-------|
| **Nama** | Siti Manager Keuangan |
| **Email** | manager.keuangan@hospital.com |
| **Username** | siti.keuangan |
| **Password** | password |
| **Role** | Keuangan |
| **Departemen** | Departemen Keuangan |
| **Permissions** | • view_dashboard<br>• approve_pum |

**Login:**
```
Username: siti.keuangan
Password: password
```

---

### 3. **Direktur** (Approval Level 3 - Final)
| Field | Value |
|-------|-------|
| **Nama** | Direktur PUM |
| **Email** | direktur@pum.test |
| **Username** | direktur.pum |
| **Password** | password |
| **Role** | Direktur |
| **Departemen** | Direktur Utama |
| **Permissions** | • view_dashboard<br>• approve_pum |

**Login:**
```
Username: direktur.pum
Password: password
```

---

## 📝 User Lainnya (Bukan Approver)

### 4. **Staff** (Pengaju PUM)
| Field | Value |
|-------|-------|
| **Nama** | Staff PUM |
| **Email** | staff@pum.test |
| **Username** | staff.pum |
| **Password** | password |
| **Role** | Staff |
| **Departemen** | - (Staff umum) |
| **Permissions** | • view_dashboard<br>• manage_pum |

**Login:**
```
Username: staff.pum
Password: password
```

**Catatan:** Staff dapat membuat permintaan PUM tetapi tidak dapat menyetujui.

---

### 5. **Administrator** (Super Admin)
| Field | Value |
|-------|-------|
| **Nama** | Administrator |
| **Email** | admin@example.com |
| **Username** | admin |
| **Password** | password |
| **Role** | Admin |
| **Departemen** | - |
| **Permissions** | ALL (Semua permissions) |

**Login:**
```
Username: admin
Password: password
```

---

## 🔄 Alur Approval Workflow

```
┌─────────────────────────────────────────────────────────────┐
│                    WORKFLOW APPROVAL PUM                     │
└─────────────────────────────────────────────────────────────┘

1️⃣  PENGAJUAN
    User: Staff PUM (staff.pum)
    Action: Membuat permintaan uang muka
    ↓

2️⃣  APPROVAL MANAGER (Level 1)
    User: Manager PUM (manager.pum)
    Action: Menyetujui/Menolak permintaan
    ↓

3️⃣  APPROVAL KEUANGAN (Level 2)
    User: Siti Manager Keuangan (siti.keuangan)
    Action: Menyetujui/Menolak permintaan
    ↓

4️⃣  APPROVAL DIREKTUR (Level 3 - Final)
    User: Direktur PUM (direktur.pum)
    Action: Menyetujui/Menolak permintaan (Final Decision)
    ↓

✅  APPROVED / ❌ REJECTED
```

---

## 📊 Ringkasan User Berdasarkan Role

### **Approvers (Yang Bisa Approve)**
1. **manager.pum** - Manager (Level 1)
2. **siti.keuangan** - Keuangan (Level 2)
3. **direktur.pum** - Direktur (Level 3)

### **Requesters (Yang Bisa Mengajukan)**
1. **staff.pum** - Staff
2. **manager.pum** - Manager (bisa mengajukan dan approve)

### **Super Admin**
1. **admin** - Administrator (akses penuh)

---

## 🔑 Quick Login Reference

### Untuk Testing Approval Workflow:

**Step 1 - Buat Permintaan:**
```
Login sebagai: staff.pum
Password: password
```

**Step 2 - Approve Level 1:**
```
Login sebagai: manager.pum
Password: password
```

**Step 3 - Approve Level 2:**
```
Login sebagai: siti.keuangan
Password: password
```

**Step 4 - Approve Level 3 (Final):**
```
Login sebagai: direktur.pum
Password: password
```

---

## 📌 Catatan Penting

1. **Semua password default adalah:** `password`
2. **User Keuangan PUM (keuangan@pum.test) SUDAH DIHAPUS** - Sekarang menggunakan Siti Manager Keuangan dari Departemen Keuangan
3. **Role 'user' SUDAH DIHAPUS** - Tidak digunakan lagi dalam sistem
4. **Setiap departemen hanya memiliki 1 manager dan 1 staff**
5. **Workflow approval bersifat sequential** - harus melewati semua level secara berurutan

---

## 🏢 User Departemen Lainnya (Bukan Approval PUM)

Berikut adalah user dari departemen lain yang juga ada di sistem:

| Departemen | Manager | Staff |
|------------|---------|-------|
| **SIRS** | budi.sirs | citra.sirs |
| **Keuangan** | siti.keuangan | rina.keuangan |
| **Sekretaris** | erna.sekretaris | fitri.sekretaris |
| **Keperawatan** | hana.keperawatan | indah.keperawatan |
| **Rawat Inap** | kiki.ranap | lina.ranap |
| **IGD** | nana.igd | oscar.igd |

**Semua password:** `password`

**Format email:** `[username]@hospital.com`

---

## ✅ Cara Menjalankan Seeder

Untuk membuat semua user ini, jalankan:

```bash
# Fresh migration + seed
php artisan migrate:fresh --seed

# Atau hanya seed
php artisan db:seed
```

---

**Last Updated:** 2026-01-06
**System:** PUM (Permintaan Uang Muka)
