@extends('auth-layout')
@section('title','Menunggu persetujuan')
@section('content')
<div class="pending-symbol"><x-icon name="shield" size="34"/></div><div class="auth-heading"><span class="auth-kicker">AKUN BERHASIL DIBUAT</span><h2>Satu langkah<br>menuju ruang kerja<span>.</span></h2><p>Halo, {{ auth()->user()->name }}. Akun Anda menunggu persetujuan administrator HSE.</p></div>
<div class="auth-help"><x-icon name="history" size="22"/><p>Menunggu peninjauan<br><span>{{ auth()->user()->email }}<br>Area: {{ auth()->user()->area }}</span></p></div><p class="auth-hint">HSE akan menentukan peran Anda. Hubungi tim HSE jika membutuhkan bantuan. Periksa status kembali setelah akun disetujui.</p><a class="auth-submit" href="/dashboard">Periksa status akses <x-icon name="arrow" size="19"/></a><form method="post" action="/logout" class="auth-exit">@csrf<button type="submit">Keluar dari akun</button></form>
@endsection