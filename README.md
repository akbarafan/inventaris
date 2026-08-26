# Aplikasi Inventaris SMK

Aplikasi manajemen inventaris barang sekolah berbasis Laravel + MySQL.

> **Untuk AI agent / programmer yang melanjutkan proyek ini:** baca [AGENTS.md](AGENTS.md) dulu — berisi arsitektur, logika bisnis kritis, riwayat progres, dan roadmap.

## Fitur

- **Manajemen Barang**: CRUD barang dengan kondisi (Baik/Rusak/Rusak Berat), kode barang otomatis (`{lokasi}-{kategori}-{inisial}`) dengan mitigasi duplikat
- **Master Data**: Kategori, Lokasi, dan Sumber Dana — masing-masing dengan kode unik otomatis; hapus diblokir jika masih dipakai barang
- **Stok Multi-Lokasi & Mutasi**: stok fisik per lokasi (`barang_lokasis`), mutasi antar lokasi
- **Scan QR Code**: scan via kamera HP (PWA) atau input manual; halaman detail publik `/b/{kode}`
- **Import CSV**: preview + koreksi per-baris di browser sebelum submit (parser CSV bawaan, tanpa dependensi eksternal)
- **Export Laporan**: CSV kompatibel Excel (BOM UTF-8, delimiter `;`) dengan filter kondisi/kategori/sumber/tanggal
- **Cetak Label Aset**: label barcode + QR batch (100×80mm)
- **Manajemen Pengguna**: role admin/petugas/user, reset password
- **Audit Log**: riwayat aktivitas otomatis (observer Barang + login/logout)
- **Pengaturan Sekolah**: nama sekolah + logo

## Persyaratan Sistem

- PHP 8.3+
- MySQL 8+ (produksi). Untuk development lokal tanpa MySQL bisa SQLite
- Composer 2.x
- Laragon / XAMPP (opsional)

## Instalasi

1. Clone atau extract project ke folder web server.

2. Copy file `.env.example` menjadi `.env`, lalu sesuaikan konfigurasi database:
   - Default `.env.example` memakai **SQLite** (`DB_CONNECTION=sqlite`) — cukup untuk development cepat.
   - Untuk MySQL (disarankan, sesuai produksi):
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=inventaris_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. Install dependency & setup:
   ```bash
   composer install
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
   ```

4. Akses di browser: `http://127.0.0.1:8000`

## Login Default

| Role    | Email                  | Password   |
|---------|------------------------|------------|
| Admin   | admin@inventaris.com   | admin123   |
| Petugas | petugas@inventaris.com | petugas123 |

## Struktur Database

| Tabel | Isi |
|---|---|
| `users` | Pengguna (admin/petugas/user) + kolom `is_active` |
| `kategoris` | Kategori barang (kode otomatis) |
| `lokasis` | Lokasi penyimpanan (kode otomatis) |
| `sumbers` | Sumber dana pengadaan (BOS, APBD, dll — kode otomatis) |
| `barangs` | Data barang + kondisi (baik/rusak/rusak_berat) + `sumber_id` FK |
| `barang_lokasis` | Stok barang per lokasi (unique barang_id+lokasi_id) |
| `scan_logs` | Log scan QR code |
| `activity_logs` | Audit trail aktivitas user |
| `settings` | Key-value pengaturan sekolah (nama, logo) |

Catatan penting:
- **Tidak ada soft delete** — semua hapus bersifat permanen (hard delete).
- Invariant stok: `jumlah = baik + rusak + rusak_berat`.

## Testing

```bash
composer test
```

Menggunakan SQLite in-memory (konfigurasi di `phpunit.xml`) — tidak menyentuh database lokal/produksi.

## Deployment (Shared Hosting)

Setelah `git pull` di server:

```bash
composer install --no-dev --optimize-autoloader   # hanya kalau composer.json berubah
php artisan migrate --force                        # aman; migrasi bersifat additif/backfill
php artisan optimize:clear
```

Detail lengkap (termasuk catatan keterbatasan ekstensi hosting) ada di [AGENTS.md](AGENTS.md).

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
