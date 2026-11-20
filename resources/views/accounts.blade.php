@extends('layout')
@section('title','Kelola akun')
@section('content')
<div class="heading"><div><div class="eyebrow">AKSES & TANGGUNG JAWAB</div><h1>Kelola akun</h1><p>Akun baru langsung aktif sesuai peran yang dipilih saat mendaftar. Ubah peran, area, atau hapus akun bila diperlukan.</p></div><span class="badge">{{ $accounts->total() }} akun</span></div>
@error('account')<div class="notice error">{{ $message }}</div>@enderror
@error('role')<div class="notice error">{{ $message }}</div>@enderror
@error('area')<div class="notice error">{{ $message }}</div>@enderror
<section class="panel"><div class="table-wrap"><table class="account-table"><thead><tr><th>Pengguna</th><th>Peran & area</th><th>Terdaftar</th><th></th></tr></thead><tbody>
@foreach($accounts as $account)
<tr><td><b>{{ $account->name }}</b>@if($account->id===auth()->id()) <span class="badge">Anda</span>@endif<span class="sub">{{ $account->email }}</span></td>
<td><form class="account-form" method="post" action="/accounts/{{ $account->id }}">@csrf
<select name="role" aria-label="Peran {{ $account->name }}" @disabled($account->id===auth()->id())>@foreach(\App\Http\Controllers\AccountController::ROLES as $value=>$label)<option value="{{ $value }}" @selected($account->role===$value)>{{ $label }}</option>@endforeach</select>
<select name="area" aria-label="Area {{ $account->name }}"><option value="">Semua area</option>@foreach(config('k3.areas') as $area)<option @selected($account->area===$area)>{{ $area }}</option>@endforeach</select>
@if($account->id===auth()->id())<input type="hidden" name="role" value="hse">@endif
<button type="submit" class="secondary">Simpan</button></form></td>
<td>{{ $account->created_at?->format('d M Y') }}</td>
<td>@if($account->id!==auth()->id())<x-delete-button compact :action="'/accounts/'.$account->id" :label="'Hapus akun '.$account->name" :title="'Hapus akun?'" :message="'Akun '.$account->email.' akan dihapus dan tidak bisa login lagi. Laporan dan data yang pernah dibuat akun ini tetap tersimpan.'"/>@endif</td></tr>
@endforeach
</tbody></table></div><div class="pagination">{{ $accounts->links() }}</div></section>
@endsection
