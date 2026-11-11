# Sistem Perpustakaan Universitas Gajayana Malang

Aplikasi Laravel 11 untuk pendaftaran anggota mahasiswa, validasi petugas, kartu anggota ber-QR, pengelolaan kategori/buku, serta katalog publik. Antarmuka menggunakan Blade dan Tailwind CSS dengan bahasa Indonesia. Tidak mencakup peminjaman, pengembalian, denda, reservasi, atau buku digital.

## Menjalankan hasil proyek di komputer ini

Dependensi, build aset, `.env`, dan database telah disiapkan. Aplikasi menggunakan **MySQL Laragon pada port 3306**.

1. Buka Laragon dan klik **Start All**, atau pastikan layanan MySQL berjalan.
2. Dari PowerShell di folder proyek, jalankan:

```powershell
.\scripts\start-local.ps1
```

3. Buka alamat lokal atau alamat LAN yang ditampilkan helper. Jangan tutup terminal web server selama aplikasi digunakan.

Helper memeriksa koneksi database, lalu menjalankan server localhost dan server pada satu alamat LAN privat yang sedang aktif. Alamat LAN diperiksa ulang tiap 5 detik; saat DHCP menggantinya, server LAN berpindah otomatis. Biarkan terminal helper tetap terbuka. Helper tidak membuat instance MySQL terpisah atau menjalankan seeder setiap startup.

Jika pemantau sudah berjalan, helper akan menampilkan alamat saat ini. Untuk menghentikan server proyek yang dijalankan helper, gunakan `.\scripts\stop-local.ps1`.

### Akses QR dari jaringan LAN

Helper menampilkan IPv4 privat dari jaringan yang sedang aktif, misalnya `http://192.168.1.x:8000`. Saat DHCP memberikan IP lain, pemantau memindahkan server ke alamat LAN aktif tanpa mengubah `.env`. QR kartu membuka halaman verifikasi anggota dari database melalui alamat LAN aktif, termasuk ketika kartu dibuka dari localhost. Foto dan cover buku menggunakan alamat relatif sehingga ikut berpindah bersama alamat halaman.

Setelah komputer dimulai ulang, jalankan MySQL Laragon lalu `.\scripts\start-local.ps1` lagi. Ponsel harus berada di jaringan yang dapat mengakses komputer; firewall atau isolasi perangkat Wi-Fi dapat menghalangi koneksi. **QR pada kartu cetak/PDF menyimpan alamat pada saat dicetak.** Jika IP DHCP berubah, kartu lama perlu dicetak ulang. Agar kartu fisik tetap berlaku lintas jaringan tanpa cetak ulang, dibutuhkan nama domain/hostname yang selalu dapat diakses beserta DNS atau layanan pengalihan yang stabil; IP LAN saja tidak dapat menjamin hal itu.

Jika sudah tersedia nama tetap, arahkan nama tersebut ke aplikasi melalui DNS dinamis atau pengalihan yang tetap, lalu isi `MEMBERSHIP_VERIFICATION_BASE_URL=https://nama-domain-anda` di `.env` dan jalankan `php artisan config:clear`. QR baru akan memakai nama itu meski IP LAN server berubah. Konfigurasi URL saja tidak menyediakan DNS maupun koneksi dari luar jaringan; uji alamat tersebut dari ponsel sebelum mencetak kartu. QR lama yang sudah dicetak berisi IP tetap perlu dicetak ulang satu kali setelah nama tetap aktif.

**Jika muncul SQLSTATE[HY000] [2002]:** periksa MySQL Laragon sudah aktif dan `.env` menggunakan `DB_HOST=127.0.0.1`, `DB_PORT=3306`. Setelah mengubah `.env`, jalankan `php artisan config:clear`, lalu muat ulang halaman.

Pada 2 November 2026, database dari instance sementara port 3307 dipindahkan ke MySQL Laragon port 3306 tanpa menghapus data. Checksum seluruh tabel cocok. Salinan database lama dan backup SQL tetap berada di `.runtime`, yang tidak masuk Git. Konfigurasi saat ini menggunakan port 3306.

Web root Apache/Nginx wajib menunjuk folder **public**, bukan root proyek. Jangan menjalankan `migrate:fresh` untuk memperbaiki koneksi karena perintah tersebut menghapus tabel dan datanya.

## Instalasi di komputer lain

### Berbagi aplikasi lewat tautan

Repo GitHub membagikan source code, bukan menjalankan aplikasi. Agar orang lain langsung memakai aplikasi lewat tautan tanpa memasang Laravel, proyek perlu di-deploy ke hosting yang mendukung PHP/Laravel dan database MySQL, lalu diberi domain HTTPS. GitHub Pages tidak menjalankan aplikasi Laravel. Pengguna akan membuka alamat situs melalui browser tanpa instalasi; data dan berkas harus disiapkan di hosting secara terpisah. Atur konfigurasi produksi di environment hosting; jangan unggah `.env`, backup database, foto anggota, atau dokumen KTM ke repo. QR pada kartu harus dibuat memakai domain tersebut agar dapat dibuka dari ponsel di luar jaringan lokal.

Prasyarat: PHP 8.2+ (yang diuji: 8.3), Composer 2, Node.js/npm, MySQL 8. Ekstensi PHP: PDO MySQL, PDO SQLite untuk test, mbstring, openssl, tokenizer, XML/DOM, ctype, fileinfo, GD, serta ZIP untuk Composer. Gunakan terminal Laragon agar `php` dan `composer` tersedia di PATH.

```sh
composer install
npm ci
cp .env.example .env
php artisan key:generate
```

Pada PowerShell, pengganti `cp` adalah `Copy-Item .env.example .env`. Buat database melalui MySQL:

```sql
CREATE DATABASE perpustakaan_gajayana CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Atur `.env`:

```dotenv
APP_NAME="Perpustakaan Universitas Gajayana Malang"
APP_ENV=local
APP_DEBUG=false
APP_URL=http://127.0.0.1:8000
APP_LOCALE=id
APP_TIMEZONE=Asia/Jakarta
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpustakaan_gajayana
DB_USERNAME=root
DB_PASSWORD=
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
MAIL_MAILER=log
MAIL_FROM_ADDRESS=perpustakaan@gajayana.ac.id
```

Kemudian:

```sh
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Jika Windows menolak symlink, buat junction di PowerShell setelah memastikan `public/storage` belum ada:

```powershell
New-Item -ItemType Junction -Path public/storage -Target (Resolve-Path storage/app/public).Path
```

Untuk pengembangan CSS/JS, jalankan `npm run dev` di terminal kedua. Setelah mengubah konfigurasi `.env`, jalankan `php artisan optimize:clear`. Untuk penggunaan LAN, gunakan helper agar server terikat pada IP privat aktif dan berpindah saat DHCP berubah. Buka kartu lewat alamat LAN sebelum mencetaknya; `APP_URL` hanya menjadi alamat dasar untuk perintah CLI.

## Akun dan data contoh

- Email admin: **admin@gajayana.ac.id**
- Untuk instalasi baru, isi `ADMIN_INITIAL_PASSWORD` di `.env` dengan password kuat sebelum `php artisan migrate --seed`. Variabel ini tidak disimpan di Git. Hapus nilainya setelah admin berhasil dibuat; seeder tidak mengubah password akun yang sudah ada.
- Seeder: 1 admin jika password awal disediakan, 5 kategori, 20 buku ilustratif, termasuk 4 buku stok habis.
- Judul, penulis, penerbit, ISBN, dan jumlah stok seed adalah data contoh, bukan inventaris resmi kampus.
- Seeder dapat dijalankan ulang tanpa menggandakan data atau mereset password admin yang sudah ada. Tidak ada akun mahasiswa bawaan; daftarkan melalui `/daftar`.

Jika sebelumnya telah memakai kredensial admin contoh dari versi lokal, ganti password sebelum aplikasi diakses pihak lain. Password otomatis di-hash oleh model `App\Models\User`. Tidak dibuat fitur reset password karena di luar lingkup.

## Revisi pendaftaran dan kartu anggota

- Pilihan program studi dan fakultas tersedia di `config/program_studi.php`; fakultas dihitung ulang di server. Pendaftaran baru tidak meminta angkatan. Data angkatan lama tidak dihapus, dan fakultas lama diisi otomatis hanya jika nama program studi cocok.
- Password baru minimal delapan karakter serta harus memuat huruf besar, huruf kecil, angka, dan simbol. Form menampilkan pilihan lihat password, konfirmasi sebelum kirim, serta soal penjumlahan yang divalidasi di server dan berlaku sekali selama 10 menit. CAPTCHA ini sederhana; pembatasan pendaftaran juga tetap berlaku.
- Admin dapat menambah atau mengganti cover JPG, PNG, atau WebP maksimal 2 MB pada form buku. Buku lama akan menunjukkan bahwa cover belum tersedia sampai petugas mengunggahnya.
- Tanggal terbit kartu dicatat saat admin menyetujui pendaftaran; KTA dan halaman verifikasi menampilkan tanggal lengkap. Untuk persetujuan sebelum kolom ini ditambahkan, migrasi mengisi tanggal dari `updated_at` hanya jika anggota disetujui dan waktu perubahan lebih baru daripada pendaftaran. Data tanpa jejak waktu yang memadai tetap ditampilkan sebagai belum tercatat.
- Kartu depan dan belakang memakai rasio ID‑1 85,6 × 53,98 mm. Cetak menempatkan masing-masing sisi pada halaman A4 terpisah agar dapat dicetak bolak-balik; pilih skala 100% dan balik sisi panjang. Kartu digital menampilkan foto, nama, NIM, program studi, fakultas, dan tanggal terbit.
- Lambang kampus memakai PNG dari [situs UPT Perpustakaan Pascasarjana UNIGA](https://library.pasca.unigamalang.ac.id/images/default/logo.png), pada subdomain resmi `unigamalang.ac.id`.
- QR memakai alamat LAN aktif dari helper atau alamat halaman kartu dan token verifikasi anggota. Pemindaian membuka halaman publik berisi kartu digital dan status anggota terbaru dari database. Halaman ini tidak menampilkan email, nomor HP, KTM, atau password. Perubahan IP setelah kartu dicetak tetap memerlukan cetak ulang kartu atau alamat domain yang stabil.
- Admin dapat mengirim ulang hasil verifikasi dari detail pendaftar yang sudah disetujui atau ditolak; alasan penolakan yang tersimpan ikut digunakan. Konfigurasi saat ini `MAIL_MAILER=log`, sehingga email belum dikirim ke inbox. Isi konfigurasi SMTP pada `.env` dan uji pengiriman melalui penyedia email sebelum menggunakan tombol tersebut. Aplikasi menampilkan peringatan saat masih memakai mode pengujian.

## Alur penggunaan dan pemeriksaan tiap tahap

1. **Database/model/seeder:** `php artisan migrate --seed`. Pastikan terdapat `t_user`, `t_anggota`, `t_kategori`, `t_buku`; selain itu Laravel menyimpan metadata migration di `migrations`. Tabel kategori dan buku tidak memakai timestamps, sesuai skema. Primary key dan foreign key memakai nama yang ditentukan.
2. **Autentikasi/role:** masuk sebagai admin melalui `/login`; mahasiswa masuk ke `/anggota/kartu`. Pengunjung diarahkan ke login saat membuka admin; mahasiswa menerima 403 jika mencoba route admin. Login dibatasi 10 request/menit/IP dan 5 kegagalan/menit/email+IP; daftar dibatasi 5 request/jam/IP.
3. **Keanggotaan:** daftar dengan foto JPG/PNG masing-masing maksimal 2 MB. NIM dan email wajib unik. Akun serta anggota disimpan dalam satu transaksi dan mahasiswa langsung masuk untuk melihat status pending. Admin memeriksa pendaftar/foto, lalu menyetujui atau menolak dengan alasan. Hanya pending yang dapat diputuskan; keputusan ganda ditolak. Setelah disetujui, mahasiswa dapat mencetak kartu. Tombol cetak juga mendukung Save as PDF bawaan browser; tidak ada paket PDF tambahan.
4. **Kategori/buku:** tambah/ubah/hapus melalui dashboard. Kategori yang masih dipakai tidak dapat dihapus. Form buku memvalidasi semua field wajib, ISBN unik, tahun empat digit tidak melebihi tahun berjalan, stok integer nonnegatif, dan tanggal valid. Kesalahan menampilkan pesan per field dan mempertahankan input.
5. **Katalog:** buka `/katalog` tanpa login, cari judul/pengarang/ISBN, pilih kategori, pindah halaman, buka detail; badge stok, jumlah, dan lokasi rak mencerminkan database.
6. **Dashboard/UI:** periksa jumlah pending, anggota disetujui, judul buku, serta buku stok habis. Navigasi responsif, tabel dapat digeser pada layar kecil, dan kartu memiliki stylesheet cetak.

## Email dan berkas

`MAIL_MAILER=log` menulis email ke `storage/logs/laravel.log`; email **tidak dikirim ke alamat sungguhan**. Empat Mailable mencakup pendaftaran diterima, notifikasi admin, disetujui, dan ditolak. Dashboard menyediakan daftar pending sebagai notifikasi yang tetap tersimpan.

Untuk Mailtrap gunakan `MAIL_MAILER=smtp` dan host, port, username/password SMTP sandbox dari akun Anda. `MAIL_SCHEME=null` untuk STARTTLS atau `smtps` bila sesuai konfigurasi provider. Jangan commit kredensial. Pengiriman berjalan setelah transaksi selesai; jika SMTP gagal, data/keputusan tetap tersimpan dan halaman menampilkan peringatan. Tidak ada retry otomatis; status dapat dilihat di akun mahasiswa.

Foto setengah badan disimpan di `storage/app/public/anggota/foto`, dengan nama acak. **KTM disimpan di `storage/app/private/ktm`**, bukan disk publik, untuk memenuhi kewajiban admin-only tanpa bergantung pada aturan web server. `storage:link` hanya membuka disk publik. Endpoint `/admin/pendaftar/{anggota}/ktm` memerlukan role admin dan memberikan header `private, no-store`.

`QRcode` menyimpan token acak 64 karakter, bukan ID berurutan. QR SVG dibuat oleh `simplesoftwareio/simple-qrcode` saat menampilkan kartu. Halaman publik verifikasi menampilkan foto, nama, NIM, prodi, fakultas, tanggal terbit, status, dan instansi penerbit; tidak memuat angkatan, email, nomor HP, KTM, atau password. Cap “VALIDASI QR DIGITAL” menandai pemeriksaan status secara daring; kolom tanda tangan tetap kosong untuk pejabat berwenang. Pengesahan resmi harus diberikan petugas kampus. Pengamanan form menggunakan CSRF, session regeneration, password hashing Laravel, validasi Form Request, middleware role, serta constraint database.

## Pengujian

Desain antarmuka menggunakan kontras gelap-terang, judul editorial, dan aksen biru yang diadaptasi dari [World Class Edge](https://www.worldclassedge.com/) untuk konteks perpustakaan. Interaksi mengikuti [Vercel Web Interface Guidelines](https://vercel.com/design/guidelines): fokus keyboard terlihat, target sentuh 44 px, input mobile 16 px, filter tersimpan di URL, status selalu berupa teks, dan tabel bisa digeser pada layar kecil. Implementasi tetap Laravel Blade/Tailwind. Favicon berada di `public/favicon.svg` dan `public/favicon.ico`; komponen bersama ada di `resources/views/components` dan token CSS di `resources/css/app.css`.

```sh
php artisan test --compact
npm run build
php artisan view:cache
```

PHPUnit memakai **SQLite in-memory** dengan `RefreshDatabase`, storage palsu, dan mail palsu pada batas pengiriman; tidak menghapus database development. Kasus mencakup registrasi/upload/rollback, duplikasi NIM/email, akses lintas role, KTM privat, persetujuan/penolakan/QR, kegagalan SMTP, template email, validasi CRUD, pencarian/filter/pagination, seeder, dan dashboard. SQLite tidak membuktikan locking MySQL pada permintaan bersamaan; migration/seed serta pemeriksaan runtime MySQL dilakukan terpisah.

## Versi dependensi

Versi framework dipertahankan **Laravel 11.57.1** sesuai permintaan. Saat instalasi, Composer melaporkan 5 advisory keamanan pada framework; resolver memerlukan `--no-security-blocking` untuk versi 11 yang diminta. Pengaturan audit global tidak dinonaktifkan dan lockfile disertakan. **Proyek ini ditujukan untuk pembelajaran lokal; sebelum deployment publik, migrasikan ke versi Laravel yang masih menerima perbaikan keamanan dan selesaikan temuan `composer audit`.** Jika mengubah dependency, lakukan evaluasi advisory, jangan melewati pemeriksaan keamanan tanpa meninjaunya.

Dokumentasi versi yang dipakai: [Laravel 11 authentication](https://laravel.com/docs/11.x/authentication), [filesystem](https://laravel.com/docs/11.x/filesystem), dan [middleware](https://laravel.com/docs/11.x/middleware).
