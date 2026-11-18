# 🏁 Go-Kart Racing Hub (Laravel)

Sistem informasi manajemen sirkuit go-kart berbasis web: pembalap bisa melihat paket bermain, booking sesi, ikut **Open Match** bareng pembalap lain, upload bukti pembayaran, memantau riwayat balapan, melihat leaderboard, dan punya **kartu lisensi + rank** pribadi. Admin mengelola paket, pengguna, verifikasi pembayaran, input waktu lap, dan rekap keuangan.

Versi ini adalah **rewrite penuh ke Laravel** dari versi PHP prosedural sebelumnya (`VALEQA/Gokart`) yang masih memakai `mysqli` dengan query tersebar di tiap file.

> Proyek kelompok **Full Throttle** — Kelas KH002, Tahun Akademik 2025/2026 Genap.

---

## ✨ Fitur

### Sisi Pembalap (user)

| Fitur | Keterangan |
|---|---|
| Registrasi & Login | Autentikasi Laravel (`Auth`), password di-hash `bcrypt`. |
| Booking Sesi | Pilih paket, tanggal, jam, jumlah orang; total harga dihitung otomatis. |
| **Open Match** | Sesi booking bisa dibuka untuk umum (`is_open_match`, `maks_slot_open`). Pembalap lain bisa **join/leave**, dan tiap partisipan punya tagihan + bukti transfer sendiri. |
| Pembayaran Manual | Upload bukti transfer (JPG/PNG/PDF), status menunggu verifikasi admin. Ada penjagaan agar tidak bisa upload dobel. |
| Riwayat Booking | Gabungan booking milik sendiri **dan** Open Match yang diikuti (ditandai 🤝), lengkap dengan status masing-masing. |
| Batalkan Booking | Hanya bisa saat status masih `aktif` dan belum upload bukti transfer. |
| Leaderboard | Peringkat berdasarkan overall lap maupun per sektor (S1/S2/S3). |
| Hasil Balapan | Waktu lap milik sendiri per sektor. |
| Profil + **Kartu Lisensi** | Kartu lisensi pembalap siap cetak, nomor otomatis dari ID user (`FT-0001-X`). |
| **Sistem Rank** | Rookie → Bronze (5 balapan) → Silver (15) → Gold (30) → Platinum (50), dengan progress bar menuju rank berikutnya. |
| Ganti Password & Foto Profil | Upload/hapus foto profil (disimpan di storage publik, maks 2 MB). |

### Sisi Admin

| Fitur | Keterangan |
|---|---|
| Dashboard | Ringkasan sirkuit + antrean verifikasi pembayaran **host** dan **partisipan Open Match**. |
| Setujui / Tolak Pembayaran | Setujui → status `selesai` + `total_bermain` user bertambah 1. Tolak → bukti transfer dihapus, user upload ulang. |
| Kelola Paket | CRUD paket bermain (nama, deskripsi, durasi, maksimal orang, harga). |
| Kelola Pengguna | CRUD akun pembalap/admin. |
| Input Waktu Balapan | Input sektor 1–3, `total_lap` dihitung otomatis. Kandidat mencakup host **dan** partisipan Open Match yang sudah lunas. |
| Leaderboard Admin | Pemantauan peringkat seluruh pembalap. |
| Riwayat Keuangan | Rekap transaksi/pembayaran masuk. |

Semua route selain landing/login/register berada di dalam middleware `auth`.

---

## 🧰 Teknologi

| Komponen | Versi |
|---|---|
| Framework | Laravel 13.8 |
| PHP | ^8.3 |
| Database | MySQL / MariaDB (produksi), SQLite (default `.env.example`) |
| Frontend | Blade + CSS/JS vanilla di `public/`; Vite 8 + Tailwind 4 tersedia tapi tidak wajib |
| Auth | Laravel Auth + middleware `auth` |

---

## 📁 Struktur Project

```text
akhir-Gokart/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php            # login, register, logout
│   │   ├── LandingController.php         # halaman depan + harga weekday/weekend
│   │   ├── DashboardController.php       # dashboard user
│   │   ├── BookingController.php         # booking, riwayat, detail, batalkan
│   │   ├── OpenMatchController.php       # daftar & join/leave open match
│   │   ├── PaymentController.php         # tagihan + upload bukti transfer
│   │   ├── LeaderboardController.php     # leaderboard user
│   │   ├── HasilBalapanController.php    # waktu lap user
│   │   ├── ProfileController.php         # profil, rank, kartu lisensi, foto
│   │   ├── AdminController.php           # dashboard admin + verifikasi bayar
│   │   ├── PaketController.php           # CRUD paket bermain
│   │   ├── UserController.php            # CRUD user
│   │   ├── RaceControlController.php     # input waktu balapan
│   │   ├── FinancialController.php       # riwayat keuangan
│   │   └── AdminLeaderboardController.php
│   └── Models/                           # User, Booking, PaketBermain,
│                                         # HasilBalapan, PesertaOpenMatch
├── database/migrations/                  # 11 migration (lihat skema di bawah)
├── resources/views/                      # 13 halaman user + 8 halaman admin (Blade)
├── public/
│   ├── style*.css, script*.js            # aset tampilan
│   ├── img/ranks/                        # badge rank (rookie → platinum)
│   └── bukti_transfer/                   # upload bukti pembayaran (runtime)
├── routes/web.php
└── storage/app/public/foto_profil/       # foto profil user (butuh storage:link)
```

---

## 🗄️ Skema Database

| Tabel | Kolom penting |
|---|---|
| `users` | `nama_lengkap`, `nomor_hp`, `email`, `password`, `role` (`admin`/`user`), `total_bermain`, `best_time`, `booking_aktif`, `foto_profil` |
| `paket_bermain` | `nama_paket`, `deskripsi`, `durasi_menit`, `maksimal_orang`, `harga` |
| `booking` | `user_id`, `paket_id`, `tanggal_booking`, `jam_booking`, `jumlah_orang`, `total_harga`, `bukti_transfer`, `status` (`aktif`/`selesai`/`dibatalkan`), `is_open_match`, `maks_slot_open` |
| `peserta_open_match` | `booking_id`, `user_id`, `status` (`bergabung`/`dibatalkan`), `status_bayar` (`aktif`/`selesai`), `bukti_transfer`, `waktu_bergabung` |
| `hasil_balapan` | `booking_id`, `user_id`, `sektor_1`, `sektor_2`, `sektor_3`, `total_lap` |

Seluruh skema dibuat lewat migration — tidak ada file `.sql` yang perlu diimport manual.

---

## 🚀 Cara Menjalankan

### Prasyarat
- PHP **8.3+** (`php -v`) dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`
- Composer
- MySQL / MariaDB (atau SQLite untuk coba cepat)

### 1. Install dependency
```bash
composer install
```

### 2. Buat file `.env`
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gokart
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrasi database
```bash
php artisan migrate
```

> Seeder bawaan template (`DatabaseSeeder` → `UserFactory`) masih memakai kolom `name` dan `email_verified_at` yang **tidak ada** di tabel `users` proyek ini, jadi `php artisan db:seed` akan error. Buat akun pertama lewat tinker:
>
> ```php
> // php artisan tinker
> App\Models\User::create([
>     'nama_lengkap' => 'Admin',
>     'nomor_hp'     => '08123456789',
>     'email'        => 'admin@example.com',
>     'password'     => bcrypt('password'),
>     'role'         => 'admin',
> ]);
> ```

### 4. Symlink storage (untuk foto profil)
```bash
php artisan storage:link
```

### 5. Jalankan
```bash
php artisan serve
```
Buka http://127.0.0.1:8000

### 6. Isi data awal
Login sebagai admin → menu **Kelola Paket** untuk menambah paket bermain. Tanpa paket, halaman booking kosong.

---

## 📝 Catatan Teknis

- **Folder upload.** Bukti transfer disimpan ke `public/bukti_transfer/` lewat `public_path()`. Kalau di-deploy dengan layout hosting yang memisahkan docroot (aplikasi di subfolder, `index.php` di root), controller otomatis fallback ke folder `public` satu tingkat di atas aplikasi.
- **Aset view** memakai `asset('style.css')` dan seterusnya — diasumsikan `public/` adalah docroot (struktur Laravel standar). Kalau dipindah ke layout hosting lain, path ini perlu disesuaikan.
- **Badge rank** di `public/img/ranks/*.jpg` masih berupa mockup: background kotak-kotak (checkerboard) dan caption seperti *"4. MASTER RANK"* ikut ter-render. Ganti dengan PNG transparan kalau ingin tampilan rapi.
- **`public/img/default-avatar.png`** dirujuk di halaman profil tapi belum ada di repo — user tanpa foto profil akan melihat gambar rusak sampai file itu ditambahkan.
- **Bukti transfer & foto profil user** tidak di-commit (data pribadi). Folder `public/bukti_transfer/` dan `storage/app/public/` hanya berisi `.gitignore`.

---

## 🔐 Sebelum Dipakai Publik

- Set `APP_ENV=production` dan `APP_DEBUG=false` — kalau `true`, stack trace dan kredensial database bisa tampil ke pengunjung.
- Pastikan `.env` tidak pernah ikut commit (sudah ada di `.gitignore`).
- Verifikasi pembayaran masih manual oleh admin; tidak ada payment gateway aktif (`PaymentController::webhook` dinonaktifkan, kolom `snap_token` sisa rencana integrasi Midtrans).
- Jalankan di belakang HTTPS dan set `SESSION_SECURE_COOKIE=true`.

---

## 📄 Lisensi

Proyek tugas akademik — bebas dipakai sebagai referensi belajar.
