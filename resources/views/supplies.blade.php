@extends('layout')
@section('title','Persediaan P3K')
@section('content')
<div class="heading"><div><div class="eyebrow">FIRST AID INVENTORY</div><h1>Persediaan P3K</h1><p>Pantau stok, kedaluwarsa, serta riwayat pengambilan dan pengisian.</p></div></div>@error('movement')<div class="notice error">{{ $message }}</div>@enderror
<div class="doc-grid">
@foreach($supplies as $s)
@php($editing=(string)old('supply_id')===(string)$s->id)
<section class="panel">
<div class="panel-head"><span class="badge">{{ $s->asset->code }}</span>@if(auth()->user()->role==='hse')<x-delete-button compact :action="'/supplies/'.$s->id" :label="'Hapus '.$s->name" :title="'Hapus persediaan?'" :message="'Persediaan '.$s->name.' beserta seluruh riwayat mutasinya akan dihapus permanen.'"/>@endif</div><h2 style="margin-top:18px">{{ $s->name }}</h2>
<div class="stock-total">{{ $s->stock }} <small>{{ $s->unit }}</small></div>
<p>Minimum {{ $s->minimum }} &middot; Exp. {{ $s->expiry }}</p>
@if($s->stock<=$s->minimum||$s->expiry<date('Y-m-d'))<div class="notice error">Perlu perhatian: stok minimum atau kedaluwarsa.</div>@endif
@if(auth()->user()->role==='hse')
<details class="stock-editor" @if($editing)open @endif>
<summary class="stock-edit-trigger"><x-icon name="medical" size="17"/> Ubah persediaan <span>Masuk / Keluar</span></summary>
<form method="post" action="/stock/{{ $s->id }}" data-stock-form data-stock="{{ $s->stock }}" data-unit="{{ $s->unit }}">
@csrf
<input name="supply_id" type="hidden" value="{{ $s->id }}">
<input name="submission_key" type="hidden" value="{{ $editing?old('submission_key'):(string)Str::uuid() }}">
<input name="reporter" type="hidden" value="{{ auth()->user()->name }}">
<fieldset class="stock-directions"><legend>Jenis mutasi</legend>
<label><input type="radio" name="direction" value="in" @checked(!$editing||old('direction','in')==='in')><span><x-icon name="plus" size="15"/> Tambah stok</span></label>
<label><input type="radio" name="direction" value="out" @checked($editing&&old('direction')==='out')><span><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14"/></svg> Kurangi stok</span></label>
</fieldset>
<div class="field"><label for="quantity-{{ $s->id }}">Jumlah {{ $s->unit }}</label><input id="quantity-{{ $s->id }}" type="number" name="quantity" min="1" max="10000" step="1" inputmode="numeric" value="{{ $editing?old('quantity'):'' }}" placeholder="Masukkan jumlah" required></div>
<div class="stock-preview" data-stock-preview role="status" aria-live="polite">Stok saat ini: {{ $s->stock }} {{ $s->unit }}</div>
<div class="field"><label for="purpose-{{ $s->id }}">Alasan / catatan mutasi</label><input id="purpose-{{ $s->id }}" name="purpose" value="{{ $editing?old('purpose'):'' }}" placeholder="Contoh: pengisian ulang atau pengambilan" maxlength="500" required></div>
<button type="submit"><x-icon name="check" size="16"/> Simpan mutasi</button>
</form></details>
@endif
</section>
@endforeach
</div>
<section class="panel"><div class="panel-head"><div><h2>Mutasi persediaan terakhir</h2><p>Penambahan dan pengurangan tercatat bersama nama petugas dan alasannya.</p></div></div>
<div class="table-wrap"><table><thead><tr><th>Waktu</th><th>Persediaan</th><th>Mutasi</th><th>Petugas / pengambil</th><th>Keperluan</th>@if(auth()->user()->role==='hse')<th></th>@endif</tr></thead><tbody>
@forelse($movements as $m)<tr><td>{{ $m->created_at }}</td><td>{{ $m->name }}</td><td><span class="badge {{ $m->quantity<0?'Open':'' }}">{{ $m->quantity>0?'Masuk +':'Keluar ' }}{{ $m->quantity }}</span></td><td>{{ $m->reporter }}</td><td>{{ $m->purpose }}</td>@if(auth()->user()->role==='hse')<td><x-delete-button compact :action="'/stock-movements/'.$m->id" :label="'Hapus mutasi '.$m->name" :title="'Hapus mutasi?'" :message="'Mutasi '.$m->name.' ('.($m->quantity>0?'+':'').$m->quantity.') akan dihapus dan stok dikoreksi otomatis. Gunakan ini bila mutasi salah input.'"/></td>@endif</tr>
@empty<tr><td colspan="6" class="empty">Belum ada mutasi.</td></tr>@endforelse
</tbody></table></div></section>
<script src="/stock.js?v=1" defer></script>
@endsection