<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#142e24">
<meta name="description" content="SMART K3 menghubungkan inspeksi peralatan, pelaporan bahaya, dan kesiapan tanggap darurat dalam satu sistem informasi keselamatan kerja.">
<title>SMART K3 — Setiap orang berarti.</title>
<link rel="preload" href="/images/landing/hero-illustration.svg?v=3" as="image" fetchpriority="high">
<link rel="preload" href="/fonts/plus-jakarta-sans.woff2" as="font" type="font/woff2" crossorigin><link rel="stylesheet" href="/landing.css?v=7">
<script>document.documentElement.classList.add('js-motion')</script><script src="/landing.js?v=4" defer></script>
@include('shared-experience')</head>
<body>
@php($workspace=auth()->check()?'/dashboard':'/login')
<a class="skip" href="#tentang">Lewati ke konten</a>
<div class="reading-progress" aria-hidden="true"></div>
<header class="site-header">
<a class="site-brand" href="/" aria-label="SMART K3 beranda"><span class="brand-symbol"><x-logo size="44"/></span><span>SMART K3<small>SAFETY, CONNECTED.</small></span></a>
<nav class="desktop-links" aria-label="Navigasi halaman"><span class="nav-indicator" aria-hidden="true"></span><a href="#tentang">Tentang</a><a href="#kesiapan">Kesiapan K3</a><a href="#fitur">Fitur sistem</a><a href="#cara-kerja">Cara kerja</a></nav>
<div class="header-actions"><a class="header-login" href="{{ $workspace }}">@auth Ruang kerja @else Masuk sistem @endauth <x-icon name="external" size="15"/></a><details class="mobile-navigation"><summary aria-label="Buka navigasi"><x-icon name="menu" size="23"/></summary><nav aria-label="Navigasi ponsel"><a href="#tentang">Tentang SMART K3</a><a href="#kesiapan">Kesiapan K3</a><a href="#fitur">Fitur sistem</a><a href="#cara-kerja">Cara kerja</a><a href="{{ $workspace }}">Masuk ruang kerja <x-icon name="arrow" size="17"/></a></nav></details></div>
</header>
<main>
<section class="zoom-hero" aria-labelledby="hero-heading">
<div class="hero-stage">
<div class="hero-visual"><img src="/images/landing/hero-illustration.svg?v=3" alt="Ilustrasi dua petugas K3 dengan helm dan rompi keselamatan meninjau data inspeksi pada tablet di area produksi." width="1600" height="900" fetchpriority="high"><div class="hero-shade"></div></div>
<div class="hero-copy page-width"><div class="eyebrow light"><span class="status-light"></span> SISTEM INFORMASI K3 TERPADU</div><h1 id="hero-heading">Setiap langkah<br><em>aman.</em> Setiap<br>orang berarti<span class="lime">.</span></h1><p>Hubungkan kepedulian dengan tindakan.<br>Kelola inspeksi, laporkan bahaya, dan jaga kesiapan<br class="desktop-break"> tempat kerja dalam satu ruang digital.</p><div class="hero-actions"><a href="{{ $workspace }}" class="pill-button">Jelajahi SMART K3 <span><x-icon name="arrow" size="20"/></span></a><a href="#tentang" class="quiet-link">Kenali lebih dekat <x-icon name="arrow" size="16" class="down-arrow"/></a></div></div>
<div class="hero-bottom page-width"><a href="#tentang" class="scroll-cue"><span class="scroll-line"></span> SCROLL UNTUK MENJELAJAHI</a><div class="hero-caption"><span>PEOPLE FIRST. SAFETY ALWAYS.</span><small>Kesiapan yang dimulai dari kita.</small></div><span class="hero-index">01 — 05</span></div>
</div>
</section>
<section class="intro-section" id="tentang">
<div class="page-width">
<div class="section-topline"><span class="eyebrow">01 / KEPEDULIAN YANG TERHUBUNG</span><span class="section-caption">Dirancang untuk ritme kerja di lapangan.</span></div>
<div class="manifesto"><div class="manifesto-label"><span class="orbit-symbol"><x-icon name="shield" size="40"/></span><p>Keselamatan adalah<br>tanggung jawab bersama.</p></div><div><h2 class="highlight-text" data-highlight>Lingkungan kerja yang aman dimulai dari kepedulian, diperkuat oleh kesiapan, dan dijaga melalui tindakan.</h2><p class="intro-description">SMART K3 membantu tim HSE, inspektor, dan penanggung jawab area mengelola informasi keselamatan secara terstruktur. Agar setiap pemeriksaan tercatat dan setiap temuan mendapat perhatian.</p></div></div>
<div class="scope-strip"><div><strong>07<span> kategori</span></strong><p>Peralatan & fasilitas tanggap darurat</p></div><div><strong>04<span> peran</span></strong><p>Kolaborasi dengan akses yang terarah</p></div><div><strong><x-icon name="qr" size="36"/> QR<span> akses</span></strong><p>Informasi peralatan dari lapangan</p></div><a href="#fitur" class="round-link" aria-label="Lihat fitur sistem"><x-icon name="arrow" size="23"/></a></div>
</div>
</section>
<section class="readiness-photo parallax-section" id="kesiapan" aria-labelledby="readiness-title">
<div class="parallax-image" data-parallax="0.16"><img src="/images/landing/inspection-illustration.svg?v=3" alt="Ilustrasi inspektor K3 memeriksa manometer alat pemadam api ringan di koridor." loading="lazy" decoding="async" width="1600" height="900"></div><div class="photo-shade"></div>
<div class="photo-layout page-width"><div class="photo-tag"><span>02 / KESIAPAN TANGGAP DARURAT</span><x-icon name="shield" size="20"/></div><div class="photo-copy"><span class="eyebrow light">DETAIL KECIL. PERAN BESAR.</span><h2 id="readiness-title">Siap sebelum<br><em>dibutuhkan.</em></h2><p>Kondisi peralatan perlu diketahui sebelum keadaan darurat terjadi. Jadikan pemeriksaan rutin sebagai bagian dari budaya kerja.</p><a class="outline-button" href="{{ $workspace }}">Mulai dari inspeksi <x-icon name="arrow" size="18"/></a></div><div class="photo-caption"><span>INSPEKSI RUTIN / PROTEKSI KEBAKARAN</span><span>Periksa. Catat. Tindak lanjuti.</span></div></div>
</section>
<section class="features-section" id="fitur">
<div class="page-width">
<div class="section-topline"><span class="eyebrow">03 / SATU SISTEM, SALING TERHUBUNG</span><span class="section-caption">Dari pemeriksaan hingga tindak lanjut.</span></div>
<div class="features-layout">
<div class="features-intro"><h2>Lebih dekat<br>dengan kondisi<br><em>di lapangan.</em></h2><p>Informasi yang jelas membantu tim menentukan langkah berikutnya.</p><figure class="feature-photo parallax-section"><div class="parallax-image" data-parallax="0.1"><img src="/images/landing/qr-illustration.svg?v=3" alt="Ilustrasi pemindaian QR pada kotak P3K menggunakan ponsel." loading="lazy" decoding="async" width="1450" height="1000"></div><figcaption><x-icon name="shield" size="17"/> TERHUBUNG UNTUK LEBIH SIAP</figcaption></figure></div>
<div class="feature-stories">
<article class="feature-story" data-scroll-feature><div class="story-heading"><span>01</span><x-icon name="clipboard" size="24"/></div><h3>Inspeksi yang<br>terdokumentasi.</h3><p>Checklist untuk APAR, APAB, hidran, safety cabinet, emergency exit, kotak P3K, dan ruang P3K. Simpan hasil pemeriksaan dan temukan kembali riwayatnya.</p><details><summary>Jelajahi inspeksi <x-icon name="plus" size="17"/></summary><div>Identitas inventaris, jadwal pemeriksaan, dokumentasi pendukung, dan hasil inspeksi tersimpan dalam satu sistem. Akses pengisian mengikuti peran petugas.</div></details></article>
<article class="feature-story" data-scroll-feature><div class="story-heading"><span>02</span><x-icon name="alert" size="24"/></div><h3>Bahaya yang<br>ditindaklanjuti.</h3><p>Laporkan unsafe action dan unsafe condition beserta lokasi dan foto. Pantau perubahan status dari Open, Process, hingga Closed.</p><details><summary>Kenali alur pelaporan <x-icon name="plus" size="17"/></summary><div>Laporan lapangan dapat dikirim tanpa login. HSE dan penanggung jawab area meninjau temuan serta mencatat tindakan penyelesaiannya.</div></details></article>
<article class="feature-story" data-scroll-feature><div class="story-heading"><span>03</span><x-icon name="medical" size="24"/></div><h3>Persediaan yang<br>terpantau.</h3><p>Catat stok masuk dan keluar P3K, lihat ketersediaan, dan perhatikan tanggal kedaluwarsa. Setiap mutasi memiliki riwayat yang dapat ditelusuri.</p><details><summary>Lihat pengelolaan P3K <x-icon name="plus" size="17"/></summary><div>Pengambilan dicatat melalui akses QR kotak P3K. HSE dapat mengelola mutasi persediaan; sistem menolak pengurangan yang melebihi stok.</div></details></article>
<article class="feature-story" data-scroll-feature><div class="story-heading"><span>04</span><x-icon name="folder" size="24"/></div><h3>Informasi yang<br>mudah diakses.</h3><p>Dashboard pemantauan, catatan kejadian, dan dokumen SOP membantu seluruh peran bekerja dari informasi yang terpusat.</p><details><summary>Kenali akses pengguna <x-icon name="plus" size="17"/></summary><div>HSE mengelola sistem, inspektor melakukan pemeriksaan, head area menindaklanjuti temuan, dan management memantau melalui akses baca.</div></details></article>
</div>
</div></div>
</section>
<section class="workflow-section" id="cara-kerja"><div class="page-width">
<div class="section-topline"><span class="eyebrow">04 / SEDERHANA UNTUK DIGUNAKAN</span><x-icon name="qr" size="25"/></div>
<div class="workflow-title"><h2>Dari lapangan,<br><em>ke tindakan.</em></h2><p>Tiga langkah untuk menghubungkan kondisi nyata dengan informasi yang bisa ditindaklanjuti.</p></div>
<div class="workflow-grid">
<article><div class="step-top"><span>01</span><x-icon name="qr" size="28"/></div><h3>Pindai & kenali.</h3><p>Scan QR pada peralatan untuk membuka identitas, lokasi, dan status inspeksi.</p></article>
<article><div class="step-top"><span>02</span><x-icon name="clipboard" size="28"/></div><h3>Periksa & catat.</h3><p>Isi inspeksi melalui akun petugas atau sampaikan temuan bahaya dari lapangan.</p></article>
<article><div class="step-top"><span>03</span><x-icon name="check" size="28"/></div><h3>Pantau & tindak lanjuti.</h3><p>Tinjau laporan dan dokumentasikan tindakan bersama penanggung jawab area.</p></article>
</div></div></section>
<section class="closing-section" id="mulai"><div class="page-width"><div class="closing-top"><span class="eyebrow light">05 / MULAI DARI SEKARANG</span><span class="closing-star" aria-hidden="true">✳</span></div><h2>Lebih siap.<br>Lebih peduli.<br><em>Lebih aman.</em></h2><div class="closing-bottom"><p>Bangun kebiasaan baik, satu pemeriksaan<br>dan satu tindakan pada satu waktu.</p><a href="{{ $workspace }}" class="pill-button">Masuk SMART K3 <span><x-icon name="arrow" size="22"/></span></a></div></div></section>
</main>
<footer class="site-footer"><div class="page-width"><div class="footer-main"><a class="site-brand" href="/"><span class="brand-symbol"><x-logo size="42"/></span><span>SMART K3<small>SAFETY, CONNECTED.</small></span></a><p>Sistem informasi keselamatan<br>dan kesehatan kerja terpadu.</p><div><a href="{{ $workspace }}">Ruang kerja <x-icon name="external" size="13"/></a>@if(!auth()->check() || auth()->user()->role==='hse')<a href="/lapor">Laporkan bahaya <x-icon name="arrow" size="13"/></a>@endif<a href="#tentang">Kembali ke atas <x-icon name="arrow" size="13" class="up-arrow"/></a></div></div><div class="footer-bottom"><span>&copy; {{ date('Y') }} SMART K3 · Prototipe penelitian</span><span>Ilustrasi vektor dibuat khusus untuk SMART K3.</span></div></div></footer>
</body></html>