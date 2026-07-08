# Aplikasi Inventaris SMK

Aplikasi manajemen inventaris barang sekolah berbasis Laravel + MySQL.

## Fitur

- **Manajemen Barang**: CRUD barang dengan rincian kondisi (Baik/Rusak/Rusak Berat)
- **Kategori & Lokasi**: Kelola kategori dan lokasi penyimpanan barang
- **Transaksi**: Catat pergerakan barang (Masuk/Keluar/Pindah)
- **Scan QR Code**: Scan kode QR barang menggunakan kamera atau upload gambar
- **Import CSV**: Import data barang dari file CSV
- **Export Excel**: Export laporan barang dan transaksi ke Excel
- **Dashboard**: Ringkasan data dengan grafik aktivitas

## Persyaratan Sistem

- PHP 8.3+
- MySQL 8.4+
- Composer 2.x
- Laragon (recommended) atau XAMPP

## Instalasi

1. Clone atau extract project ke folder Laragon (`C:\laragon\www\inventaris`)

2. Copy file `.env.example` menjadi `.env`:
   ```
   cp .env.example .env
   ```
   Atau rename manual.

3. Generate application key:
   ```
   php artisan key:generate
   ```

4. Buat database MySQL:
   ```
   mysql -u root -e "CREATE DATABASE inventaris_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

5. Pastikan konfigurasi database di `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=inventaris_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Jalankan migrasi dan seeder:
   ```
   php artisan migrate
   php artisan db:seed
   ```

7. Buat storage link:
   ```
   php artisan storage:link
   ```

8. Jalankan development server:
   ```
   php artisan serve
   ```

9. Akses di browser: `http://127.0.0.1:8000`

## Login Default

| Role     | Email                  | Password    |
|----------|------------------------|-------------|
| Admin    | admin@inventaris.com   | admin123    |
| Petugas  | petugas@inventaris.com | petugas123  |

## Struktur Database

- `users` - Data pengguna (admin/petugas)
- `kategoris` - Kategori barang
- `lokasis` - Lokasi penyimpanan
- `barangs` - Data barang dengan kolom kondisi (baik, rusak, rusak_berat)
- `barang_lokasis` - Relasi barang ke lokasi (stok per lokasi)
- `transaksis` - Riwayat transaksi barang
- `scan_logs` - Log scan QR code

## Backup Database

Jalankan file `backup.bat` untuk backup database ke folder `backup/`:
```
backup.bat
```

Atau manual:
```
mysqldump -u root inventaris_db > backup/inventaris_db_YYYY-MM-DD.sql
```

## Restore Database
```
mysql -u root inventaris_db < backup/inventaris_db_YYYY-MM-DD.sql
```
