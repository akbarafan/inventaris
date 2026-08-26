# AGENTS.md — Konteks Proyek Inventaris SMK

> Dokumen ini ditujukan untuk AI coding agent maupun programmer yang melanjutkan proyek ini.
> Baca bagian **Logika Bisnis Kritis** sebelum mengubah kode — ada beberapa perilaku yang sengaja dirancang dan jangan diubah sembarangan.

---

## 0. Gaya Kerja AI — Hemat Token, Hasil Tetap Optimal

Wajib diikuti semua agent yang bekerja di repo ini :

**Komunikasi:**
1. **Jawaban singkat, kerja lengkap.** Default jawaban maksimal ±4 baris; detail hanya kalau user minta. Satu kata/jawaban pendek lebih disukai daripada penjelasan panjang.
2. **Langsung ke inti.** Tanpa pembuka/penutup ("Tentu!", "Semoga membantu!", "The answer is...", "Here is what I will do..."), tanpa emoji, tanpa permintaan maaf berlebihan, tanpa menjelaskan ulang kode yang baru ditulis. Kalau gagal, tunjukkan error + fix.
3. **Jangan bertanya kalau bisa memutuskan.** Ambil keputusan dari konvensi di dokumen ini; tanya user hanya jika benar-benar ambigu atau berisiko (mis. hapus data produksi).
4. **Proaktif tapi jangan mengejutkan.** Kerjakan yang diminta; jangan lakukan aksi tambahan yang tidak diminta.

**Efisiensi kerja:**
5. **Jangan baca ulang file yang sama.** Satu file cukup dibaca sekali per sesi. Untuk memahami struktur, pakai pencarian (grep/glob) dengan pola spesifik — bukan membaca banyak file utuh.
6. **Edit bedah, jangan tulis ulang.** Gunakan edit ter-target pada bagian yang berubah saja. Tulis ulang seluruh file hanya untuk file baru.
7. **Batch operasi paralel.** Kumpulkan baca/cek independen dalam satu giliran (bukan satu-per-satu menunggu hasil).
8. **Verifikasi murah dulu.** `php -l`, `artisan route:list`, `view:cache` sebelum test berat. Jalankan suite penuh (`composer test`) hanya di akhir tugas.

**Disiplin kode:**
9. **Ikuti konvensi yang sudah ada** di repo (pola controller/view/route), bukan gaya pribadi.
10. **Jangan tambah komentar di kode** kecuali diminta.
11. **Ruang lingkup ketat.** Hanya ubah apa yang diminta. Perbaikan kecil di sekitarnya boleh, tapi sebutkan satu kalimat — jangan refactor diam-diam.
12. **Commit hanya saat diminta**, pesan commit singkat gaya repo (`feat:`/`fix:` + Bahasa Indonesia).
13. **Keamanan dulu.** Jangan expose secrets/credentials, ikuti security best practice, tidak ada data sensitif masuk git.

---

## 1. Ringkasan Proyek

Aplikasi manajemen inventaris barang sekolah (SMK) berbasis web.

| Aspek | Detail |
|---|---|
| Framework | Laravel 13 (`laravel/framework ^13.8`), PHP `^8.3` |
| Database | Produksi: **MySQL 8** (shared hosting). Lokal/tes: SQLite |
| Frontend | Blade + Tailwind (via layout), DataTables, vanilla JS fetch (tanpa framework SPA) |
| QR Code | `bacon/bacon-qr-code` (SVG, tanpa imagick) |
| UI | 100% Bahasa Indonesia. Semua pesan sukses/error dalam Bahasa Indonesia |
| PWA | manifest + service worker (`public/sw.js`) untuk scan via kamera HP |

Fitur utama: CRUD barang dengan kondisi (baik/rusak/rusak berat), stok per lokasi (mutasi), master data (kategori, lokasi, sumber dana), import CSV, export CSV laporan, scan QR publik, cetak label barcode batch, manajemen user & role, audit log aktivitas, pengaturan sekolah (nama + logo), dashboard grafik.

## 2. Menjalankan Lokal

```bash
composer install
cp .env.example .env        # lalu sesuaikan DB
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Login default (dari seeder): `admin@inventaris.com / admin123`, `petugas@inventaris.com / petugas123`.

### Catatan environment Windows (mesin development saat ini)

- PHP CLI terpasang di `C:\php` (`php.ini`: `pdo_mysql` aktif, **`pdo_sqlite` TIDAK aktif**, ekstensi sqlite tersedia sebagai DLL tapi di-comment).
- MySQL lokal sering tidak berjalan → untuk smoke test tanpa MySQL:
  ```powershell
  # copy php.ini ke folder temp, uncomment extension=pdo_sqlite dan extension=sqlite3
  # jalankan: php -c "path\ke\php-test.ini" artisan migrate --force
  # set env: DB_CONNECTION=sqlite, DB_DATABASE=<file.sqlite> (file harus dibuat dulu)
  ```
- Tes otomatis sudah dikonfigurasi sqlite in-memory lewat `phpunit.xml`: jalankan `composer test`.
- Driver SQLite **tidak tersedia di hosting produksi** — hanya MySQL.

### Testing

- Status: **skeleton saja** (`tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`). Belum ada suite sungguhan.
- `phpunit.xml` sudah benar: `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`, cache/session array, `BCRYPT_ROUNDS=4`.
- Belum ada trait `RefreshDatabase` dipakai; factory hanya `UserFactory` (belum punya state `admin()`/`petugas()`, dan `is_active` tidak bisa diisi massal — perlu set manual).

## 3. Arsitektur

### Model & Relasi

```
User ──hasMany──> ScanLog
Barang ──belongsTo──> Kategori, Lokasi, Sumber
Barang ──hasMany──> BarangLokasi (stok per lokasi), ScanLog
BarangLokasi ──belongsTo──> Barang, Lokasi   [unique(barang_id, lokasi_id)]
ActivityLog ──belongsTo──> User              [polymorphic-ish via model_type/model_id string]
```

- `Barang`: fillable termasuk `kode_barang, nama_barang, kategori_id, lokasi_id, sumber_id, foto, jumlah, baik, rusak, rusak_berat, keterangan, tanggal_masuk`. Kolom kondisi adalah angka.
- `Kategori`, `Sumber`: hook `creating` auto-isi `kode` = 3 huruf pertama nama (uppercase).
- `Lokasi`: hook `saving` auto-isi `kode` = inisial multi-kata atau 3 huruf pertama.
- `Setting`: helper statis `Setting::get($key)` / `Setting::set($key, $value)`. View composer global menyuntikkan `$settings` ke semua view (lihat `AppServiceProvider`).
- `ActivityLog::log()` sengaja **menelan exception** (logging tidak boleh mematahkan alur utama). Untuk assert di test, query tabel `activity_logs` langsung.
- **Tidak ada soft delete sama sekali** — kolom `deleted_at` sudah di-drop dari semua tabel (migrasi `2024_02_02_000003`).

### Observer

`BarangObserver` (daftar di `AppServiceProvider::boot`) mencatat ActivityLog untuk event `created/updated/deleted` Barang. Konsekuensi penting: pada `destroy()`, cleanup manual ActivityLog dijalankan **sebelum** `$barang->delete()`, sehingga jejak audit action `deleted` **selalu tersisa** — ini memang disengaja sebagai audit trail.

### Controller (peta cepat)

| Controller | Isi |
|---|---|
| `AuthController` | Login pakai **email ATAU name** (deteksi via `filter_var(...FILTER_VALIDATE_EMAIL)`), wajib `is_active=true`, session regenerate, log login/logout. Route login dibungkus `throttle:5,1` |
| `BarangController` | Terbesar. index/store/show/edit/update/destroy/mutasi/importCsv/printLabel/downloadQR/publicDetail/info |
| `KategoriController`, `SumberController` | CRUD sederhana + `withCount('barangs')`; hapus diblokir 422 jika masih dipakai barang (pesan menyebut jumlah) |
| `LokasiController` | CRUD + detail stok; **update kode lokasi me-regenerate semua `kode_barang` barang di lokasi itu** |
| `UserController` | CRUD user + reset password; blokir hapus diri sendiri & admin terakhir |
| `LaporanController` | Filter laporan + export CSV stream (BOM UTF-8, delimiter `;`) |
| `ScanController` | Halaman scan + endpoint publik `/scan/{kode}` |
| `AuditController` | Riwayat aktivitas, filter user/action, limit 500 |
| `SettingController` | Nama sekolah + upload logo |

### Route (pola)

- Publik: `GET /login`, `POST /login` (throttle), `POST /logout`, `GET /b/{kode}` (detail barang publik), `GET /scan/{kode}`, `GET /info` (tersembunyi, lihat bawah).
- Grup `auth`: dashboard, scan, laporan, resource `barang/kategori/lokasi/sumber`, print label, QR download.
- Sub-grup `admin` middleware: import CSV, export laporan, users CRUD, settings, audit, mutasi, serta DELETE untuk semua resource.
- Pola route campur: sebagian pakai route model binding `{barang}`, sebagian `$id` biasa — hati-hati saat menambah route baru.
- Endpoint tersembunyi: `GET /info?bismi=ak_fan` (status server) — tanpa parameter itu balikin 404.

### Views

Layout tunggal `layouts/app.blade.php` (sidebar berseksi: Menu / Pencatatan / Master / Administrasi). Pola umum halaman master data: tabel + modal + JS fetch JSON + DataTables (locale Indonesia). Modal edit mengisi form dari endpoint `GET /xxx/{id}/edit` yang balikin JSON mentah.

## 4. Logika Bisnis Kritis — JANGAN DIUBAH SEMBARANGAN

### 4.1 Generator kode barang
Format: `{lokasi.kode}-{kategori.kode}-{inisial(nama)}` contoh `RKL-ELE-PROJ`, inisial maks 5 karakter, fallback `X`/`XX`/`XXX`.

- `Barang::createBarangUnique(array $data, ?string $customKode = null)`:
  - Maks **50 percobaan**; suffix `-2`, `-3`, ... saat collision.
  - Deteksi duplicate lintas driver: pesan MySQL `"Duplicate entry"`, Postgres `23505`, SQLite `"UNIQUE constraint failed"` — **jangan hilangkan salah satu cabang** (lokal pakai SQLite, produksi MySQL).
  - Dibungkus `DB::transaction`.
- Ada mitigasi race condition di sini (commit `37b79fd`, `af4a7c8`). Jangan ganti ke pola check-then-insert biasa.

### 4.2 Invariant stok
`jumlah = baik + rusak + rusak_berat` — divalidasi di store/update/importCsv/mutasi (422 jika dilanggar). Stok fisik per lokasi ada di `barang_lokasis` (jumlah/baik/rusak/rusak_berat per baris); total barangs adalah agregat.

### 4.3 Mutasi stok antar lokasi (`BarangController@mutasi`)
- Tolak jika lokasi tujuan == lokasi asal (422).
- Stok kurang → tolak (422).
- Baris sumber habis → **dihapus**; jika lokasi asal jadi kosong, `barang.lokasi_id` di-repoint ke tujuan.
- Selalu log ActivityLog.

### 4.4 Import CSV
Pakai **parser CSV bawaan** (fgetcsv) — BUKAN Maatwebsite Excel.

> ⚠️ `maatwebsite/excel` sengaja **dihapus** karena gagal ter-install di shared hosting (ekstensi tidak tersedia). **Jangan tambahkan lagi.** Kalau butuh excel, generate CSV yang kompatibel Excel seperti `exportBarang()` sekarang (BOM + `;`).

Perilaku: preview dulu di frontend (parse CSV di JS), user bisa koreksi per-baris (termasuk pilih `sumber_id` per baris + sumber default), submit array `rows[]`. Validasi per-baris dikumpulkan ke array `errors` di respons JSON. Lokasi via param `ruang` memakai `firstOrCreate`.

### 4.5 Hard delete total
Aplikasi ini **tanpa soft delete**. `BarangController@destroy`:
1. Hapus file foto (disk public).
2. Hapus `barangLokasis`, `scanLogs`, `ActivityLog` milik barang (query by `model_type = Barang::class AND model_id`).
3. `$barang->delete()` → observer menulis ulang SATU entri audit `deleted` (sengaja dibiarkan).
Semua dalam transaksi. User secara eksplisit minta "jangan sampai nyisa" — jangan introvert-kan soft delete lagi.

### 4.6 Error rendering JSON
`bootstrap/app.php`:
```php
$exceptions->shouldRenderJsonWhen(
    fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
);
```
Bagian `|| $request->expectsJson()` **wajib ada**. Tanpa itu, validasi gagal di semua form fetch AJAX balikin 302 HTML sehingga alert error JS tidak pernah muncul. (Bug ini pernah terjadi.)

### 4.7 Konvensi respons & UI
- Respons JSON selalu: `{ success: bool, message: string, data?: ... }`. Error validasi standar Laravel 422 (JS membaca `res.message`).
- Hapus master data yang masih direferensikan → HTTP 422 dengan pesan Bahasa Indonesia yang menjelaskan (mis. "masih dipakai 2 barang"), bukan hapus diam-diam.
- Sidebar berseksi: Menu / Pencatatan / Master / Administrasi (admin only).

## 5. Deployment (Shared Hosting)

Prosedur setelah `git pull` di server:

```bash
composer install --no-dev --optimize-autoloader   # kalau composer.json berubah
php artisan migrate --force                        # selalu aman; migrasi bersifat additif/backfill
php artisan optimize:clear                         # config, route, view cache
```

Catatan:
- Migrasi besar yang sudah terlanjur jalan di produksi: `sumbers` table + backfill `sumber_id` dari kolom `sumber` lama (kolom string di-drop setelah backfill — data lama aman), drop `deleted_at`.
- Seeder hanya untuk install fresh; `SumberSeeder` idempotent (`firstOrCreate`) tapi `UserSeeder` **tidak** (pakai `create`).
- Hosting: ekstensi PHP terbatas (tidak ada sqlite driver, shell_exec kadang di-disable — kode sudah di-guard, lihat commit `dae359f`).

## 6. Riwayat Progres Besar (timeline)

Urut dari lama:
1. Fitur dasar inventaris (barang/kategori/lokasi/transaksi awal, scan QR, PWA + service worker, scan publik redirect `/b/{kode}`).
2. Cetak label aset batch dengan barcode + QR (serangkaian fix proporsi label 100x80mm).
3. Halaman status server `/info?bismi=ak_fan` + guard `shell_exec`.
4. Manajemen user (role admin/petugas/user, reset password, guard hapus).
5. Mitigasi race condition pembuatan kode barang (`createBarangUnique` retry).
6. Import CSV update jumlah existing barang (bukan selalu create).
7. **Hapus soft delete total** (trash page, restore, force-delete dihapus).
8. **Tabel `sumbers`** — pengganti kolom string hardcoded `sumber`; relasi `sumber_id` FK nullable `nullOnDelete`; migrasi backfill otomatis dari data lama; drop `deleted_at` (`86a4211`).
9. **Halaman Master Sumber** (CRUD + blokir hapus + sidebar berseksi) + fix JSON error rendering (`f60b981`).

Commit terbaru relevan: `f60b981`, `86a4211`, `0440e2f`, `af4a7c8`, `37b79fd`.

## 7. Kelemahan Diketahui / Teknis Utang

- **Test suite masih skeleton** — belum ada test fitur sama sekali. Prioritas tertinggi untuk kualitas.
- `UserSeeder` tidak idempotent (duplicate email kalau dijalankan dua kali).
- Campuran route model binding (`{barang}`) vs `$id` di controller — rawan keliru saat refactor route.
- `/info` diam-diam expose info server via query param rahasia — bukan auth sungguhan.
- `generateKodeBarang()` versi lama masih ada (check-then-insert, race-prone) — dipakai di beberapa jalur regenerasi kode lokasi; jalur create utama sudah aman via `createBarangUnique`.
- Foto barang: hapus file pakai `Storage::disk('public')` — pastikan symlink storage benar di hosting.

## 8. Roadmap / Yang Harus Dilakukan Berikutnya

1. **Buat suite test PHPUnit** (prioritas): unit `createBarangUnique`/initials/kodeBase; feature store/update/destroy barang (termasuk cleanup hard delete), mutasi, import CSV, guard hapus kategori/lokasi/sumber, login+throttle+inactive user, AdminMiddleware 403, regenerate kode saat ubah kode lokasi. Pakai `RefreshDatabase`, `Storage::fake('public')`, `postJson/deleteJson` agar dapat 422 JSON.
2. Tambah state factory User (`admin()`, `petugas()`, `inactive()`) + factory Barang/Kategori/Lokasi/Sumber.
3. Rapikan route model binding vs `$id` jadi konsisten.
4. Pertimbangkan policy/gate daripada cek `isAdmin()` manual di view.
5. Ganti endpoint `/info` dengan auth sungguhan kalau mau lebih aman.

---

*Terakhir diperbarui: Agustus 2026, setelah commit `f60b981`.*
