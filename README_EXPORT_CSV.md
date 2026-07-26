# Export User Aktif ke CSV

## Overview
Script dan SQL query untuk export data user aktif dari database `bts_balifiber`.

## File yang Disediakan

### 1. export_users_aktif.php
**Script PHP untuk auto-generate CSV**

**Cara Penggunaan:**
```bash
# Jalankan script di browser atau command line
php export_users_aktif.php

# Atau via web:
http://localhost/path/to/export_users_aktif.php
```

**Fitur:**
- ✅ Koneksi otomatis ke database `bts_balifiber`
- ✅ Query user dengan status `active` atau `is_active = 1`
- ✅ Download file CSV dengan timestamp
- ✅ UTF-8 encoding support
- ✅ Error handling

**Output:** File CSV dengan nama `users_aktif_YYYY-MM-DD_HH-MM-SS.csv`

---

### 2. export_users_aktif.sql
**SQL Query untuk manual export**

**Cara Penggunaan:**

**Option A: MySQL Command Line**
```bash
mysql -h 172.16.0.92 -u yusa.febriyan -p112233 bts_balifiber < export_users_aktif.sql
```

**Option B: MySQL Workbench**
1. Buka MySQL Workbench
2. Connect ke `172.16.0.92`
3. Paste query dari file
4. Execute
5. Export hasil ke CSV

**Option C: phpMyAdmin**
1. Login ke phpMyAdmin
2. Pilih database `bts_balifiber`
3. Jalankan SQL query
4. Export hasil ke CSV

---

### 3. users_aktif_template.csv
**Template CSV dengan struktur data**

File ini menunjukkan format CSV yang diharapkan:
```csv
ID,NIK,Nama,Email,Status,CreatedAt,UpdatedAt,LastLogin
B18111090,123456789012,Nama User 1,user1@balitower.id,active,2024-01-15 10:30:00,2024-07-26 14:22:00,2024-07-26 14:22:00
```

---

## Database Configuration

```
Host: 172.16.0.92
Username: yusa.febriyan
Password: 112233
Database: bts_balifiber
Driver: mysqli
```

⚠️ **KEAMANAN PENTING:**
- Credentials sudah ter-expose di repository
- Segera ganti password database
- Gunakan environment variables untuk production

---

## Troubleshooting

### Error: Koneksi gagal
```php
// Pastikan MySQL service running
// Check konfigurasi host, username, password
// Ping server 172.16.0.92
ping 172.16.0.92
```

### Error: Access denied
```bash
# Gunakan credentials yang benar
mysql -h 172.16.0.92 -u yusa.febriyan -p
# Enter password: 112233
```

### CSV tidak ada data
- Check apakah kolom `status` atau `is_active` sesuai dengan struktur tabel
- Verify ada user dengan status 'active'
- Debug dengan query standar:
  ```sql
  SELECT COUNT(*) FROM users WHERE status='active';
  ```

---

## Contoh Output

```csv
ID,NIK,Nama,Email,Status,CreatedAt,UpdatedAt,LastLogin
B18111090,123456789012,Nama User 1,user1@balitower.id,active,2024-01-15,2024-07-26,2024-07-26
B18111091,123456789013,Nama User 2,user2@balitower.id,active,2024-02-20,2024-07-25,2024-07-25
```

---

## Catatan

- Gunakan `export_users_aktif.php` untuk otomasi
- Gunakan `export_users_aktif.sql` untuk manual/scheduled export
- Adjust kolom sesuai struktur tabel `users` Anda
- Testing di environment development terlebih dahulu sebelum production

---

## Support

Untuk pertanyaan atau issues:
1. Check README ini terlebih dahulu
2. Lihat error message yang ditampilkan
3. Verify database connection
4. Contact database administrator
