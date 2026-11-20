@extends('auth-layout')
@section('title','Buat akun')
@section('content')
<div class="auth-heading"><span class="auth-kicker">BERGABUNG DENGAN TIM</span><h2>Mulai langkah<br>yang lebih aman<span>.</span></h2><p>Isi data singkat di bawah. Akun langsung aktif dan Anda bisa segera masuk ke ruang kerja.</p></div>
<div class="auth-tabs"><a href="/login">Masuk</a><a href="/register" aria-current="page">Buat akun <x-icon name="arrow" size="15"/></a></div>
@include('messages')
<form action="/register" method="post" class="auth-form">@csrf
<div class="field"><label for="name">Nama lengkap</label><input id="name" name="name" value="{{ old('name') }}" autocomplete="name" placeholder="Nama sesuai identitas" required maxlength="100"></div>
<div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" placeholder="nama@perusahaan.com" required maxlength="255"></div>
<div class="field"><label for="role">Peran</label><select name="role" id="role" required>@foreach(\App\Http\Controllers\AccountController::SELF_ROLES as $value=>$label)<option value="{{ $value }}" @selected(old('role','inspector')===$value)>{{ $label }}</option>@endforeach</select></div><div class="field"><label for="area">Area kerja</label><select name="area" id="area" required><option value="">Pilih area kerja</option>@foreach(config('k3.areas') as $area)<option @selected(old('area')===$area)>{{ $area }}</option>@endforeach</select></div>
<div class="auth-field-pair">@foreach(['password'=>'Kata sandi','password_confirmation'=>'Konfirmasi kata sandi'] as $id=>$label)<div class="field"><label for="{{ $id }}">{{ $label }}</label><div class="auth-password"><input id="{{ $id }}" name="{{ $id }}" type="password" autocomplete="new-password" minlength="8" required aria-describedby="password-hint"><button type="button" data-auth-password="{{ $id }}" aria-label="Tampilkan {{ strtolower($label) }}" aria-pressed="false"><x-icon name="eye" size="18"/></button></div></div>@endforeach</div>
<p class="auth-hint" id="password-hint">Minimal 8 karakter.</p>
<button class="auth-submit" type="submit" data-magnetic>Buat akun <x-icon name="arrow" size="19"/></button>
</form><p class="auth-switch">Sudah punya akun? <a href="/login">Masuk di sini</a></p>
@endsection