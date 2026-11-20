@extends('auth-layout')
@section('title','Masuk')
@section('content')
<div class="auth-heading"><span class="auth-kicker">SELAMAT DATANG KEMBALI</span><h2>Langkah baik,<br>dimulai di sini<span>.</span></h2><p>Masuk untuk melanjutkan aktivitas keselamatan kerja Anda.</p></div>
<div class="auth-tabs"><a href="/login" aria-current="page">Masuk</a><a href="/register">Buat akun <x-icon name="arrow" size="15"/></a></div>
@include('messages')
<form action="/login" method="post" class="auth-form">@csrf
<div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@perusahaan.com" autocomplete="username" required maxlength="255"></div>
<div class="field"><label for="password">Kata sandi</label><div class="auth-password"><input id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan kata sandi" required><button type="button" data-auth-password="password" aria-label="Tampilkan kata sandi" aria-pressed="false"><x-icon name="eye" size="19"/></button></div></div>
<button class="auth-submit" type="submit" data-magnetic>Masuk ke ruang kerja <x-icon name="arrow" size="19"/></button>
</form><p class="auth-switch">Baru di SMART K3? <a href="/register">Buat akun sekarang</a></p>
<div class="auth-help"><x-icon name="lock" size="20"/><p>Lupa kata sandi?<br><span>Hubungi administrator HSE untuk bantuan akses akun.</span></p></div>
@endsection