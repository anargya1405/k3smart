# Visual landing page SMART K3

## Ilustrasi vektor (20 November 2026) — aset aktif
Foto AI diganti dengan ilustrasi kartun SVG orisinal yang digambar langsung dalam kode (tanpa AI generator, tanpa aset pihak ketiga, tanpa lisensi tambahan).
- public/images/landing/hero-illustration.svg — dua petugas K3 (salah satunya berhijab) meninjau tablet di area produksi; hero.
- public/images/landing/inspection-illustration.svg — inspektor memeriksa manometer APAR di koridor; bagian parallax kesiapan.
- public/images/landing/qr-illustration.svg — pemindaian QR kotak P3K dengan ponsel; inset bagian fitur.
Animasi ringan ada di dalam SVG (lampu berkedip, ban berjalan, jarum manometer, sinar pindai) dan dimatikan saat prefers-reduced-motion.
Foto lama k3-team-hero.png dan k3-inspection.png sudah dihapus; halaman login/daftar dan banner dashboard kini juga memakai hero-illustration.svg.

## Perubahan interaksi (20 November 2026)
- Loading screen pembuka (smart-loader) dihapus dari experience.js dan unified.css.
- Efek scroll (zoom hero, parallax, progress, sorotan teks) memakai easing/lerp agar tidak patah-patah.
- Konten muncul bertahap saat discroll (reveal), hero masuk dengan stagger saat halaman dibuka, angka cakupan menghitung naik.
- Hover tombol dan kartu alur kerja lebih halus; ilustrasi hero bergeser sedikit mengikuti kursor (hanya pointer presisi).

## Arsip: foto AI sebelumnya
Dibuat menggunakan tool image_gen bawaan. Dua gambar adalah ilustrasi AI, bukan foto dokumentasi perusahaan. Pengungkapan tersedia pada footer landing page.

## Aset final
- public/images/landing/k3-team-hero.png — foto ilustratif tim inspeksi di pabrik laminating; hero dan inset fitur.
- public/images/landing/k3-inspection.png — foto ilustratif pemeriksaan APAR; bagian parallax kesiapan.
Keduanya disalin ke workspace dari hasil tool. Aset dimuat lokal tanpa CDN.

## Prompt hero (verbatim)
Create a premium editorial documentary-style photograph for an Indonesian workplace safety (K3) software website hero. Wide landscape 16:9 composition. A clean modern plastic laminating factory hall with orderly metal production lines, skylights and warm morning sunlight. Two Indonesian safety inspectors wearing correctly fastened white hard hats, clear protective safety glasses, lime reflective safety vests over dark forest-green long sleeve uniforms and safety boots, standing on a clearly marked safe pedestrian walkway, reviewing a tablet together. People and equipment concentrated on right half and middle distance; left third has softly lit factory space with enough darker unobtrusive negative space for large white web headline. Sophisticated cinematic forest green and warm ivory color grading, subtle atmospheric depth, realistic skin and hands, natural candid poses, shot with 35mm lens, high-end architectural corporate photography, exceptionally professional. No text, no logos, no watermark, no collage, no UI. Realistic and calm focus on prevention and readiness, no accidents or dangerous behavior. This is a project asset intended as a full-bleed parallax background.

## Prompt inspeksi (verbatim)
Create a single wide cinematic editorial photograph, landscape 16:9, for SMART K3 workplace safety website parallax section. A qualified Indonesian safety inspector in dark forest-green long-sleeve uniform, lime reflective vest, correctly fitted white hardhat, clear protective goggles and safety shoes performing a visual inspection of a bright red upright wall-mounted fire extinguisher in a spotless modern industrial factory corridor. Inspector on the LEFT third, seen from side at medium distance, holding a small tablet with one hand and examining the extinguisher's pressure gauge with the other without activating the extinguisher. Extinguisher intact, pin and hose present, clearly accessible. Minimal clean muted sage-grey concrete wall and softly lit factory corridor extend across the RIGHT half, ample negative space for white editorial typography. Warm natural side lighting, realistic people and fingers, authentic materials, sophisticated desaturated green and warm cream color grade with the red extinguisher as focal accent, 50mm architectural documentary photography, quiet and professional. No legible text, no company logos, no watermark, no montage, no UI, no fire or accidents.

## Referensi interaksi
- https://motion.dev/examples/react-scroll-zoom-hero — pembesaran foto hero, blur ringan, dan fade teks mengikuti posisi scroll.
- https://motion.dev/examples/react-parallax — foto bergerak dengan kecepatan berbeda dari konten.
- https://motion.dev/examples/js-scroll-highlight — sorotan konten berdasarkan posisinya di viewport.

Implementasi dibuat khusus menggunakan Blade, CSS, requestAnimationFrame, dan JavaScript lokal; source premium Motion+ tidak disalin. Tidak ada penguncian scroll. prefers-reduced-motion menonaktifkan gerakan.

## Rute
- / — landing page publik untuk tamu maupun pengguna yang sudah login.
- /dashboard — dashboard internal terproteksi; inspektor dan head area diarahkan ke modul sesuai peran.
- /login — login dan redirect ke dashboard.

## Validasi
13 tes aplikasi / 59 assertions lulus setelah penyesuaian rute.
14 pemeriksaan browser lulus: halaman publik, pemuatan foto, perubahan skala hero, highlight bertahap, perpindahan parallax, sorotan fitur, viewport 390 px tanpa overflow, menu ponsel, Escape, reduced motion, dan tidak ada exception JavaScript.
Screenshot hasil pemeriksaan tersedia di storage/qa/landing-desktop.png, landing-mobile-390.png, landing-features.png, landing-parallax.png.
