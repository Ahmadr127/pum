# 🔐 Daftar User Login - PUM System

## User Approval Workflow

### 1. Manager Departemen (Approval Level 1)
**Pilih salah satu manager dari departemen:**

```
Manager SIRS
Username : budi.sirs
Password : password

Manager Keuangan (juga Approver Level 2)
Username : siti.keuangan
Password : password

Manager Sekretaris
Username : erna.sekretaris
Password : password

Manager Keperawatan
Username : hana.keperawatan
Password : password

Manager Rawat Inap
Username : kiki.ranap
Password : password

Manager IGD
Username : nana.igd
Password : password
```

### 2. Keuangan (Approval Level 2)
```
Nama     : Siti Manager Keuangan
Username : siti.keuangan
Password : password
```

### 3. Direktur (Approval Level 3 - Final)
```
Nama     : Dr. Ahmad Direktur
Username : direktur.utama
Password : password
```

---

## User Lainnya

### Administrator
```
Nama     : Administrator
Username : admin
Password : password
```

---

## User Departemen Lengkap

### Direktur Utama
```
Nama     : Dr. Ahmad Direktur
Username : direktur.utama
Password : password
Role     : Direktur (Approver Level 3)
```

### Departemen SIRS
```
Manager  : Budi Manager SIRS
Username : budi.sirs
Password : password
Role     : Manager (Approver Level 1)

Staff    : Citra Staff SIRS
Username : citra.sirs
Password : password
```

### Departemen Keuangan
```
Manager  : Siti Manager Keuangan
Username : siti.keuangan
Password : password
Role     : Keuangan (Approver Level 2)

Staff    : Rina Staff Keuangan
Username : rina.keuangan
Password : password
```

### Departemen Sekretaris
```
Manager  : Erna Manager Sekretaris
Username : erna.sekretaris
Password : password
Role     : Manager (Approver Level 1)

Staff    : Fitri Staff Sekretaris
Username : fitri.sekretaris
Password : password
```

### Departemen Keperawatan
```
Manager  : Hana Manager Keperawatan
Username : hana.keperawatan
Password : password
Role     : Manager (Approver Level 1)

Staff    : Indah Staff Keperawatan
Username : indah.keperawatan
Password : password
```

### Departemen Rawat Inap
```
Manager  : Kiki Manager Rawat Inap
Username : kiki.ranap
Password : password
Role     : Manager (Approver Level 1)

Staff    : Lina Perawat Ranap
Username : lina.ranap
Password : password
```

### Departemen IGD
```
Manager  : Nana Manager IGD
Username : nana.igd
Password : password
Role     : Manager (Approver Level 1)

Staff    : Oscar Perawat IGD
Username : oscar.igd
Password : password
```

---

## 🔄 Workflow Approval

```
Level 1: Manager Departemen (budi.sirs / erna.sekretaris / hana.keperawatan / dll)
   ↓
Level 2: Keuangan (siti.keuangan)
   ↓
Level 3: Direktur (direktur.utama) - FINAL
```

---

## 📝 Catatan
- **Semua password:** `password`
- **Total User:** 14 users
  - 1 Direktur Utama
  - 6 Manager Departemen
  - 6 Staff Departemen
  - 1 Admin
- **User Manager PUM dan Staff PUM:** DIHAPUS (tidak digunakan)
- **Approval Level 1:** Bisa dilakukan oleh manager departemen manapun
