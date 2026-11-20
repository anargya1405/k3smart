<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#f4f3eb"><title>@yield('title','Dashboard') &middot; SMART K3</title><link rel="stylesheet" href="/app.css?v=13"><link rel="stylesheet" href="/theme.css?v=13"><link rel="stylesheet" href="/navigation.css?v=15"><script src="/ui.js?v=4" defer></script>@include('shared-experience')</head><body class="top-navigation">
<a class="skip-link" href="#main-content">Lewati ke konten</a>
@php
$groups=[
 'operasional'=>['Operasional','Rutinitas yang menjaga kesiapan.','Kelola peralatan, pemeriksaan, dan kebutuhan tanggap darurat.',[
 ['/assets','box','Inventaris K3','Identitas, lokasi & kesiapan alat','assets'],
 ['/module/inspections','clipboard','Inspeksi rutin','Checklist dan riwayat pemeriksaan','inspections'],
 ['/module/schedule','calendar','Jadwal inspeksi','Agenda & penanggung jawab','schedule'],
 ['/supplies','medical','Persediaan P3K','Ketersediaan & mutasi persediaan','supplies']]],
 'pemantauan'=>['Pemantauan','Dari temuan menjadi tindakan.','Pantau laporan dan koordinasikan langkah pencegahan.',[
 ['/module/reports','alert','Safety report','Temuan bahaya & tindak lanjut','reports'],
 ['/module/incidents','activity','Riwayat kecelakaan','Catatan kejadian & klasifikasi','incidents']]],
 'pustaka'=>['Pustaka','Pengetahuan yang mudah dijangkau.','Temukan panduan kerja dan riwayat aktivitas sistem.',[
 ['/module/documents','folder','Dokumen & SOP','Prosedur dan instruksi kerja','documents'],
 ['/accounts','shield','Kelola akun','Peran & akses pengguna','accounts'],
 ['/audit','history','Jejak audit','Riwayat perubahan data','audit']]]];
$actions=[];
foreach(['inspections'=>['clipboard','Inspeksi baru','Mulai pemeriksaan peralatan'],'reports'=>['alert','Safety report','Catat potensi bahaya'],'schedule'=>['calendar','Jadwal baru','Rencanakan pemeriksaan'],'incidents'=>['activity','Catat kejadian','Dokumentasikan kejadian'],'documents'=>['folder','Unggah dokumen','Simpan SOP & instruksi kerja']] as $key=>$item){
 if(\App\Http\Controllers\K3Controller::writable($key))$actions[]=['/module/'.$key.'/new',...$item];
}
$quick=$actions;
if(!$quick){foreach([['/module/reports','alert','Tinjau laporan','Lihat tindak lanjut','reports'],['/assets','box','Lihat inventaris','Pantau kondisi peralatan','assets'],['/module/documents','folder','Buka SOP','Temukan panduan kerja','documents']] as $a)if(\App\Http\Controllers\K3Controller::allowed($a[4]))$quick[]=array_slice($a,0,4);}
@endphp
<header class="navigation-header"><div class="nav-inner">
<a class="brand top-brand" href="/" aria-label="SMART K3, halaman utama"><span class="brand-icon"><x-logo size="40"/></span><span>SMART K3<small>SAFETY MANAGEMENT</small></span></a>
<nav class="desktop-navigation" aria-label="Navigasi utama"><span class="nav-indicator" aria-hidden="true"></span>
@if(in_array(auth()->user()->role,['hse','management']))<a class="nav-trigger {{ request()->is('dashboard')?'current':'' }}" href="/dashboard" @if(request()->is('dashboard'))aria-current="page"@endif>Overview</a>@endif
@foreach($groups as $key=>$group)
@php($items=array_values(array_filter($group[3],fn($item)=>in_array($item[4],['audit','accounts'])?auth()->user()->role==='hse':\App\Http\Controllers\K3Controller::allowed($item[4]))))
@if(count($items))
<details class="mega-menu" data-popover><summary class="nav-trigger {{ collect($items)->contains(fn($a)=>str_starts_with(request()->getPathInfo(),$a[0]))?'current':'' }}" aria-controls="mega-{{ $key }}">{{ $group[0] }}<svg class="chevron" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="m3 4.5 3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></summary>
<div class="mega-panel" id="mega-{{ $key }}"><div class="mega-intro"><span class="menu-eyebrow">SMART WORKSPACE</span><h2>{{ $group[1] }}</h2><p>{{ $group[2] }}</p><div class="menu-stamp"><x-logo size="44"/><span>Keselamatan dalam<br>setiap langkah.</span></div></div><div class="mega-content"><div class="menu-section-title">{{ $group[0] }} <span>{{ count($items) }} modul</span></div><div class="mega-grid">
@foreach($items as $item)<a class="mega-link" style="--item-index:{{ $loop->index }}" href="{{ $item[0] }}" @if(str_starts_with(request()->getPathInfo(),$item[0]))aria-current="page"@endif><span class="menu-icon"><x-icon :name="$item[1]" size="22"/></span><span><b>{{ $item[2] }}</b><small>{{ $item[3] }}</small></span><x-icon name="arrow" size="15" class="link-arrow"/></a>@endforeach
</div><div class="mega-bottom"><span class="online-dot"></span> Akses sesuai peran <span>{{ auth()->user()->role==='hse'?'Administrator HSE':ucfirst(auth()->user()->role) }}</span></div></div></div></details>
@endif @endforeach</nav>
<div class="nav-tools">
@if(count($actions))<details class="create-menu" data-popover><summary class="create-trigger" aria-label="Buat data baru" aria-controls="create-panel" data-magnetic><x-icon name="plus" size="18"/><span>Buat baru</span></summary><div class="create-panel" id="create-panel"><div class="create-panel-heading"><span class="menu-eyebrow">MULAI DARI SINI</span><h2>Apa yang ingin kamu buat?</h2><p>Pilih tindakan untuk melanjutkan.</p></div><div class="create-grid">@foreach($actions as $a)<a href="{{ $a[0] }}" style="--item-index:{{ $loop->index }}"><span class="menu-icon"><x-icon :name="$a[1]" size="23"/></span><b>{{ $a[2] }}</b><small>{{ $a[3] }}</small><x-icon name="arrow" size="15" class="link-arrow"/></a>@endforeach</div></div></details>@endif
@include('notifications')
<details class="account-menu" data-popover><summary class="account-trigger" aria-label="Menu akun {{ auth()->user()->name }}"><span class="avatar">{{ mb_substr(auth()->user()->name,0,1) }}</span></summary><div class="account-panel"><span class="menu-eyebrow">AKUN ANDA</span><h3>{{ auth()->user()->name }}</h3><p>{{ auth()->user()->email }}</p><span class="badge">{{ ['hse'=>'Administrator HSE','management'=>'Top Management','inspector'=>'Inspektor','head'=>'Head Area'][auth()->user()->role] }}</span><form method="post" action="/logout">@csrf<button><x-icon name="logout" size="17"/>Keluar dari akun</button></form></div></details>
</div></div></header>
<div class="shell"><main id="main-content"><div class="page-context"><div><span class="context-dot"></span> Ruang kerja perusahaan <span class="context-separator">/</span> <strong>@yield('title','Dashboard')</strong></div><span class="workspace-label"><span class="online-dot"></span> Lingkungan demonstrasi</span></div>
@include('messages') @yield('content')
<footer class="footer"><span><x-icon name="shield" size="13"/> SMART &middot; Keselamatan dalam setiap langkah.</span><span>Prototipe penelitian &middot; {{ date('Y') }}</span></footer></main></div>
@if(count($quick))<details class="floating-actions" data-popover><summary class="fab-trigger" aria-label="Buka aksi cepat" aria-controls="fab-list" data-magnetic><x-icon name="plus" size="25"/></summary><div class="fab-list" id="fab-list"><span class="fab-heading">AKSI CEPAT</span>@foreach(array_slice($quick,0,4) as $a)<a href="{{ $a[0] }}" style="--item-index:{{ $loop->index }}"><span class="fab-label">{{ $a[2] }}</span><span class="fab-icon"><x-icon :name="$a[1]" size="20"/></span></a>@endforeach</div></details>@endif
@include('confirm-dialog')<div class="magnetic-ring" aria-hidden="true"></div></body></html>