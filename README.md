# Event4U

Event4U adalah aplikasi web untuk menemukan acara dan membeli tiket secara online. Aplikasi ini mendukung pembelian tiket oleh pengguna terdaftar maupun tamu, pembayaran melalui Midtrans, serta pengelolaan acara untuk administrator.

## Fitur Utama

### Untuk pengunjung dan pembeli

- Melihat daftar acara, detail acara, kategori, lokasi, dan jadwal penjualan.
- Melihat pilihan tiket dan melakukan pemesanan sebagai tamu atau pengguna terdaftar.
- Membayar pesanan melalui integrasi Midtrans.
- Memeriksa status pembayaran dan pesanan.
- Mengunduh e-ticket dalam format PDF dengan QR code.
- Melihat riwayat pesanan dan mengelola profil setelah masuk ke akun.

### Untuk administrator

- Mengelola acara, kategori, dan tiket.
- Mengatur informasi acara seperti deskripsi, lokasi, jadwal, thumbnail, dan layout panggung.
- Melihat daftar pesanan dan pembayaran.
- Memperbarui status pembayaran.
- Melihat analitik penjualan per acara maupun seluruh acara.
- Mengekspor laporan analitik ke Excel atau PDF.

## Alur Pembelian

1. Pengunjung memilih acara dan jenis tiket.
2. Pengunjung mengisi data pemesan. Akun tidak wajib untuk pembelian tamu.
3. Sistem membuat pesanan dengan batas waktu pembayaran.
4. Pembayaran diproses melalui Midtrans.
5. Setelah pembayaran berhasil, e-ticket dapat diunduh sebagai PDF.

## Teknologi

- PHP 8.2 atau lebih baru
- Laravel 12
- SQLite sebagai database bawaan konfigurasi lokal, atau database lain yang didukung Laravel
- Vite, Tailwind CSS, dan Alpine.js
- Midtrans untuk pembayaran
- Simple Software QR Code untuk QR code pada e-ticket
- Dompdf untuk PDF
- Laravel Excel untuk ekspor laporan
- Pest dan PHPUnit untuk pengujian

## Persyaratan

Pastikan perangkat telah memiliki:

- PHP >= 8.2
- Composer
- Node.js dan npm
- Ekstensi PHP yang dibutuhkan Laravel, termasuk `pdo_sqlite` jika menggunakan SQLite
- Kredensial akun Midtrans Sandbox untuk menguji pembayaran

## Instalasi Lokal

Clone repositori lalu masuk ke direktori proyek:

```bash
git clone <url-repositori>
cd Event4U
```

Pasang dependensi backend dan frontend:

```bash
composer install
npm install
```

Siapkan environment aplikasi:

```bash
copy .env.example .env
php artisan key:generate
```

Perintah `copy` di atas digunakan pada Windows. Pada macOS atau Linux, gunakan `cp .env.example .env`.

Secara default, aplikasi menggunakan SQLite. Buat file database jika belum tersedia, lalu jalankan migrasi dan seeder:

```bash
type nul > database\database.sqlite
php artisan migrate --seed
```

Pada macOS atau Linux, gunakan `touch database/database.sqlite` sebagai pengganti `type nul > database\database.sqlite`.

## Konfigurasi Environment

Sesuaikan nilai berikut di `.env`:

```dotenv
APP_NAME=Event4U
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_MERCHANT_ID=your-merchant-id
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

Gunakan kredensial Sandbox selama pengembangan. Endpoint notifikasi Midtrans harus dapat diakses oleh server Midtrans ketika menguji callback pembayaran. Jangan commit file `.env` atau kredensial pembayaran ke repositori.

## Menjalankan Aplikasi

Untuk menjalankan server Laravel dan Vite secara terpisah:

Terminal pertama:

```bash
php artisan serve
```

Terminal kedua:

```bash
npm run dev
```

Aplikasi tersedia di `http://localhost:8000`.

Alternatifnya, gunakan script development yang juga menjalankan queue listener dan log viewer:

```bash
composer run dev
```

Untuk membuat build frontend production:

```bash
npm run build
```

## Akun dan Hak Akses

Sistem membedakan pengguna biasa dan administrator melalui atribut `role` pada tabel pengguna. Route administrator berada di bawah prefix `/admin` dan dilindungi middleware admin. Data awal aplikasi dapat dibuat melalui:

```bash
php artisan db:seed
```

Detail akun administrator sebaiknya disesuaikan pada seeder atau dibuat melalui mekanisme administrasi yang digunakan pada deployment.

## Pengujian

Jalankan seluruh test dengan:

```bash
php artisan test
```

Atau jalankan Pest secara langsung:

```bash
vendor/bin/pest
```

## Struktur Direktori Penting

```text
app/                 Logika aplikasi, controller, model, mail, dan export
database/            Migrasi, factory, dan seeder
resources/views/     Template antarmuka Blade
resources/js/        Source JavaScript frontend
resources/css/       Source stylesheet
routes/web.php       Route publik, pengguna, tamu, dan administrator
config/midtrans.php  Konfigurasi integrasi Midtrans
tests/               Unit test dan feature test
```

## Lisensi

Proyek ini menggunakan lisensi MIT.
