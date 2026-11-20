# Referensi dan keputusan UI SMART

Referensi diakses 16 November 2026:
- https://mitti.com/analytics — inspirasi pengelompokan informasi inspeksi, temuan, dan inventaris pada dashboard operasional.
- https://lucide.dev/icons/ — inspirasi ikon garis yang konsisten dan bermakna. Komponen SVG lokal dibuat untuk kebutuhan aplikasi, bukan instalasi paket Lucide.

Implementasi:
- Sidebar navy, aksen emerald, kartu putih, warna amber untuk perhatian.
- Satu komponen Blade x-icon, ukuran 15–27 px, stroke konsisten, aria-hidden untuk ikon dekoratif.
- Judul dan label tetap tersedia sebagai teks; tombol ikon memiliki aria-label.
- Halaman login baru, tombol tampilkan kata sandi, email/lock icons.
- Menu mobile dengan backdrop, Escape, aria-expanded; link lewati konten.
- Grafik menampilkan angka jumlah dan legenda; KPI memakai data database.
- Font sistem, CSS/JavaScript/SVG lokal tanpa CDN.
- Pengaturan prefers-reduced-motion, focus-visible, dan tampilan cetak.
- Referensi digunakan sebagai inspirasi struktur dan gaya; tidak menyalin screenshot atau branding produk.

Validasi: kompilasi Blade, regresi fitur Laravel, HTTP login/dashboard dan aset CSS/JS.
Screenshot browser otomatis belum tersedia karena proses grafis browser gagal pada lingkungan ini.

## Navigasi atas dan interaksi Motion (16 November 2026)

Referensi yang dibaca:
- https://motion.dev/examples/vue-mega-menu
- https://motion.dev/examples/vue-floating-action-button
- https://motion.dev/examples/vue-create-button
- https://motion.dev/examples/vue-cursor-magnetic
- https://motion.dev/examples?category=base-ui

Implementasi orisinal memakai Blade, CSS, dan JavaScript lokal. Source Motion+ tidak diambil dan paket Vue/Motion tidak ditambahkan.
- Navbar atas menggantikan sidebar; mega menu Operasional, Pemantauan, Pustaka difilter berdasarkan peran.
- Indikator navigasi bergeser; panel dan item muncul bertahap.
- Buat baru membuka grid tindakan; FAB membuka daftar aksi cepat.
- Outline kursor menempel ke tombol tertentu dengan pergeseran maksimum 4px horizontal/3px vertikal. Kursor native tetap tersedia.
- Pola dropdown, dismissible toast, dan tooltip label pada FAB mengacu pada kategori Base UI.
- Native details/summary menjaga navigasi tetap dapat digunakan tanpa JavaScript.
- Keyboard: Enter/Space, panah navigasi, ArrowDown ke tautan, Escape menutup dan memulihkan fokus; klik di luar menutup.
- prefers-reduced-motion mematikan animasi; pointer kasar/layar sentuh menonaktifkan efek magnetik.
- Toast berhasil dapat ditutup; pesan error tidak otomatis dihilangkan.

Verifikasi terbaru:
- 12 tes Laravel, 51 assertions lulus.
- JavaScript lolos pemeriksaan sintaks Node.
- 10 pemeriksaan interaksi pada render HTML aplikasi dengan Chrome headless lulus.
- Screenshot desktop 1440px dan viewport sempit 500px sudah diperiksa.
- File screenshot: storage/qa/desktop.png dan storage/qa/mobile.png.
- Hasil interaksi: storage/qa/interaction-results.json.
- Verifikasi browser terbaru menggantikan keterbatasan screenshot yang dicatat pada bagian sebelumnya.

## Palet, font dan navbar baru (20 November 2026)
- Palet: teal gelap #0F3D3E (dasar), kuning safety #FFC83D (aksen utama), oranye #FF7A45 (aksen kedua), mint #8ED1B4, krem hangat #FBF7EE, teks #17302F. Diterapkan ke landing, login/daftar, dashboard dan ilustrasi SVG.
- Font: Plus Jakarta Sans (SIL OFL 1.1), disimpan lokal di public/fonts dan dimuat lewat public/fonts.css; tanpa CDN.
- Ukuran teks paragraf landing dinaikkan (±15–16,5 px) dan judul memakai bobot 700–800.
- Navbar landing mengambang (pill, blur), indikator kuning bergeser ke bagian yang sedang dibaca/di-hover, tersembunyi saat scroll ke bawah dan muncul saat scroll ke atas. Menu ponsel dibuat ulang.
- Wajah karakter pada ilustrasi dirapikan (mata dan mulut berada di dalam wajah).
