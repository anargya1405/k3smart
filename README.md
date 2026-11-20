# SMART — Sistem Informasi K3 Terpadu

Aplikasi Laravel 12 / PHP 8.2 berdasarkan Persiapan.pdf. Antarmuka Blade dengan CSS responsif khusus, tanpa ketergantungan CDN atau proses build frontend. SQLite digunakan untuk demonstrasi lokal; migrasi mendukung MySQL. Livewire/Tailwind belum digunakan: antarmuka ini memakai Blade dan CSS langsung agar dapat dijalankan dengan dependensi yang tersedia.

## Mulai cepat (setelah clone dari GitHub, Windows)
1. Pasang **XAMPP** (PHP 8.2 atau lebih baru) dan **Composer** (https://getcomposer.org/download/).
2. Clone repo ini, lalu klik dua kali **`setup.bat`** (sekali saja). Skrip ini memasang dependensi, membuat `.env`, kunci aplikasi, database SQLite, dan data contoh.
3. Klik dua kali **`jalankan.bat`**, lalu buka http://127.0.0.1:8000 di browser.
4. Masuk dengan akun demonstrasi di bawah (kata sandi `SaktiDemo!2026`).

Catatan presentasi lengkap (metode, database, fitur, pengujian) ada di `docs/Catatan-Presentasi-SMART-K3.pdf`.

## Menjalankan aplikasi
Dari PowerShell di folder k3-app:
```powershell
php artisan serve --host=127.0.0.1 --port=8000
```
Buka http://127.0.0.1:8000. Alternatif: jalankan start-local.ps1.
Dependensi, kunci aplikasi, database, dan data contoh sudah disiapkan di komputer ini.

Untuk pemasangan baru:
```
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```
Buat database/database.sqlite kosong sebelum migrasi jika memakai SQLite.
Seeder berisi data simulasi dan akan mengatur ulang kata sandi akun demo. Jangan jalankan seeder demo pada database operasional.

## Akun demonstrasi
Kata sandi semua akun: SaktiDemo!2026
- hse@sakti.test — Administrator HSE.
- management@sakti.test — Akses baca.
- inspector@sakti.test — Inspeksi, agenda, inventaris, P3K dan dokumen; mengedit inspeksi sendiri.
- head@sakti.test — Tindak lanjut temuan area Produksi A.
Akses lapangan: /lapor dan /q/{token}, tanpa login.

## Fitur
- Dashboard periode bulanan: cakupan inspeksi, laporan per area, klasifikasi kejadian dan agenda.
- Inventaris tujuh jenis; identitas QR SVG dibuat lokal dalam PHP.
- Checklist per jenis inventaris; hasil menentukan kondisi inventaris.
- Kalender agenda inspeksi dan penambahan jadwal.
- Unsafe action/condition, unggah bukti, tindak lanjut Open/Process/Closed.
- Riwayat kecelakaan Nearmiss/FAC/RWC/LTA/Fatality.
- P3K: transaksi keluar/masuk, pemeriksaan stok dan kedaluwarsa, riwayat mutasi.
- Dokumen SOP: unggah PDF/gambar dan unduh terproteksi.
- Pendaftaran akun langsung aktif: pengguna memilih peran (Inspektor, Head Area, Top Management) dan area; peran HSE tidak bisa dipilih sendiri. HSE mengubah peran/area atau menghapus akun di menu Kelola akun.
- Pengingat jadwal langsung di dalam web (tanpa email/API): ikon lonceng menampilkan status Terlambat/Hari ini/Besok/X hari lagi, dan muncul pop-up sekali setiap login bila ada jadwal terlambat atau hari ini/besok. Jadwal dapat ditandai selesai agar pengingat berhenti.
- Pengingat dalam aplikasi untuk jadwal dekat/lewat, laporan aktif, stok rendah dan kedaluwarsa. Management tidak menerima pengingat.
- Pencarian, filter, ekspor CSV dan cetak dashboard.
- Audit perubahan laporan/inventaris; mutasi stok memiliki ledger terpisah.
- CSRF, validasi server, hash kata sandi, pembatasan login/pelaporan, kontrol peran/area.
- Hapus data: HSE dapat menghapus data di semua modul (inspeksi, jadwal, safety report, kecelakaan, dokumen), inventaris, persediaan P3K, mutasi stok (stok dikoreksi otomatis), serta menolak permintaan akun. Inspektor dapat menghapus inspeksi miliknya. Setiap penghapusan meminta konfirmasi dan tercatat di jejak audit.
- Token pengiriman mencegah formulir yang sama tersimpan dua kali. Kemiripan isi laporan berbeda belum dideteksi otomatis.

## QR di ponsel
Ubah APP_URL menjadi URL LAN/domain yang dapat diakses ponsel, kemudian jalankan php artisan config:clear.
Alamat 127.0.0.1 hanya dapat diakses dari komputer ini.
Untuk demo LAN gunakan php artisan serve --host=0.0.0.0 dan alamat IP komputer.
Generator QR lokal mendukung URL hingga 106 byte. Gunakan domain yang ringkas.
Tidak ada layanan QR eksternal atau pengiriman data ke pihak ketiga.

## MySQL
Atur DB_CONNECTION=mysql, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD dalam .env, kemudian jalankan php artisan config:clear dan php artisan migrate.
Data SQLite tidak otomatis dipindahkan. Pengujian saat ini menggunakan SQLite.

## Pengujian
```
php vendor/phpunit/phpunit/phpunit --do-not-cache-result
```
Pengujian fitur mencakup render halaman, login, pembatasan peran/area, duplikasi pengiriman, stok tidak negatif, larangan pengisian publik, dan inspeksi yang memperbarui kondisi aset.
QR juga diuji melalui decode OpenCV pada tahap pengembangan, tanpa dependensi Python saat aplikasi berjalan.

## Batas implementasi dan validasi penelitian
- Checklist adalah contoh, wajib divalidasi pembimbing/HSE terhadap standar yang dipilih. Bukan klaim sertifikasi kepatuhan.
- Data seed adalah simulasi, bukan data kecelakaan perusahaan.
- Notifikasi saat ini di dalam aplikasi; pengiriman email/WhatsApp terjadwal belum diintegrasikan.
- Lokasi berupa area dan deskripsi, belum denah perusahaan aktual.
- PIC jadwal berupa nama. Penugasan akun per jadwal, arsip (soft delete) laporan, dan persetujuan revisi belum tersedia; penghapusan bersifat permanen.
- Persediaan terikat kotak P3K; katalog awal berasal dari seeder. Pengelolaan batch/tanggal kedaluwarsa baru dan penambahan jenis persediaan melalui UI belum tersedia.
- Tindak lanjut memiliki catatan; berkas bukti awal bisa diunggah pada laporan. Unggah berkas khusus penyelesaian belum tersedia.
- Grafik memakai data database saat halaman dimuat; bukan live push/WebSocket.
- Belum dipublikasikan ke hosting. Hosting Sites menggunakan Workers dan tidak menjalankan PHP; gunakan hosting PHP/MySQL untuk aplikasi ini.
- Periksa kebutuhan data pribadi, kebijakan retensi, backup, HTTPS, dan akun operasional sebelum penggunaan perusahaan.

## Logo
Logo orisinal: perisai teal dengan helm keselamatan kuning dan tanda centang mint. Komponen Blade x-logo, favicon di public/favicon.svg, logo lengkap (dengan tulisan) di public/images/logo-smart-k3.svg.

## Landing page SMART K3
Alamat utama / adalah landing page publik. Dashboard internal berada di /dashboard dan tetap memerlukan login. Setelah login, pengguna menuju dashboard sesuai perannya. Landing page menggunakan CSS/JavaScript lokal, foto ilustratif buatan AI, hero scroll zoom, parallax foto, dan sorotan teks saat scroll. Animasi dinonaktifkan saat prefers-reduced-motion aktif.


## Tema terpadu dan pendaftaran
- Landing, login, buat akun, akses lapangan, dan ruang kerja memakai token visual bersama di public/unified.css: forest, paper, lime; font lokal Segoe UI; fokus keyboard dan magnetic hover.
- Loading line reveal mengikuti konsep https://motion.dev/examples/js-loading-line-reveal, dengan implementasi CSS/JS mandiri. Tampil sekali per sesi tab, tidak mengulang saat navigasi, fallback otomatis maksimum 4,5 detik; tanpa JavaScript atau dengan reduced motion tidak menghalangi konten.
- /register: nama, email, area, dan kata sandi terkonfirmasi (minimal 10 karakter, huruf dan angka). Email dinormalisasi, kata sandi di-hash, CSRF dan pembatasan percobaan aktif.
- Pendaftaran selalu membuat role pending, tidak menerima peran dari formulir publik. Akun menunggu di /akun/menunggu dan belum boleh mengakses modul internal.
- HSE: Pustaka > Persetujuan akun (/accounts). Pilih Inspektor, Head Area, atau Top Management dan area, lalu setujui. Persetujuan dicatat dalam jejak audit. Tidak ada pemberian role HSE melalui pendaftaran.
- Persetujuan ini manual; email belum diverifikasi dan belum ada email pemberitahuan/pemulihan otomatis.
- Pengujian: 21 tes PHPUnit / 135 assertion; browser desktop 1440 px dan ponsel 390 px, formulir, login, loader, menu, hover, reduced motion, dan JavaScript. Hasil browser di storage/qa/unified-results.json (diabaikan Git).
