@if(auth()->user()->role!=='management')
@php
$role=auth()->user()->role;$notifications=collect();$today=now()->startOfDay();
$scheduleQuery=\App\Models\Entry::where('module','schedule')->where('status','Open')->where('date','<=',now()->addDays(7)->toDateString());
if($role==='head')$scheduleQuery->where('area',auth()->user()->area);
foreach($scheduleQuery->orderBy('date')->limit(10)->get() as $n){
 $days=(int)$today->diffInDays(\Carbon\Carbon::parse($n->date)->startOfDay(),false);
 $when=$days<0?'Terlambat '.abs($days).' hari':($days===0?'Hari ini':($days===1?'Besok':$days.' hari lagi'));
 $notifications->push(['title'=>$n->title,'sub'=>$n->area.' · '.\Carbon\Carbon::parse($n->date)->translatedFormat('d M Y'),'url'=>'/entry/'.$n->id,'tag'=>$when,'level'=>$days<0?'late':($days<=1?'soon':'plan'),'kind'=>'Jadwal inspeksi']);
}
if(in_array($role,['hse','head'])){$query=\App\Models\Entry::whereIn('module',['reports','inspections'])->whereIn('status',['Open','Process']);if($role==='head')$query->where('area',auth()->user()->area);foreach($query->latest()->limit(8)->get() as $n)$notifications->push(['title'=>$n->title,'sub'=>$n->area,'url'=>'/entry/'.$n->id,'tag'=>$n->status,'level'=>'info','kind'=>'Tindak lanjut']);}
if($role==='hse')foreach(\App\Models\Supply::where(fn($q)=>$q->whereColumn('stock','<=','minimum')->orWhere('expiry','<=',now()->addDays(30)->toDateString()))->limit(8)->get() as $n)$notifications->push(['title'=>$n->name,'sub'=>'Stok '.$n->stock.' '.$n->unit.' · Exp. '.$n->expiry,'url'=>'/supplies','tag'=>$n->stock<=$n->minimum?'Stok menipis':'Segera kedaluwarsa','level'=>'soon','kind'=>'Persediaan P3K']);
$urgent=$notifications->where('kind','Jadwal inspeksi')->whereIn('level',['late','soon']);
$showToast=$urgent->isNotEmpty()&&!session('schedule_toast_shown');
if($showToast)session(['schedule_toast_shown'=>true]);
@endphp
<details class="notifications"><summary aria-label="Pengingat, {{ $notifications->count() }} item"><x-icon name="bell" size="20"/>@if($notifications->count())<span class="badge notif-count{{ $urgent->isNotEmpty()?' urgent':'' }}">{{ $notifications->count() }}</span>@endif</summary><div class="notification-list"><h3>Pengingat & tindak lanjut</h3>@forelse($notifications as $n)<a href="{{ $n['url'] }}" class="notif-item {{ $n['level'] }}"><span class="notif-kind">{{ $n['kind'] }}</span><b>{{ $n['title'] }}</b><small>{{ $n['sub'] }}</small><span class="notif-tag">{{ $n['tag'] }}</span></a>@empty<p>Tidak ada pengingat saat ini.</p>@endforelse</div></details>
@if($showToast)
<div class="schedule-toast" role="status" aria-live="polite"><div class="schedule-toast-icon"><x-icon name="calendar" size="22"/></div><div><b>{{ $urgent->count() }} jadwal inspeksi perlu perhatian</b>@foreach($urgent->take(3) as $u)<a href="{{ $u['url'] }}">{{ $u['title'] }} <span class="notif-tag {{ $u['level'] }}">{{ $u['tag'] }}</span></a>@endforeach</div><button type="button" class="toast-close" aria-label="Tutup pengingat" onclick="this.closest('.schedule-toast').remove()"><x-icon name="close" size="16"/></button></div><script>(()=>{const t=document.currentScript.previousElementSibling;document.body.appendChild(t);setTimeout(()=>t.isConnected&&t.classList.add('leaving'),12000);setTimeout(()=>t.remove(),12600);})()</script>
@endif
@endif
