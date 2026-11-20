<?php
namespace App\Http\Controllers;
use App\Models\{Asset,Entry,Supply};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,DB,Storage};
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class K3Controller extends Controller {
public static function allowed($m){return match(auth()->user()?->role){'hse','management'=>true,'inspector'=>in_array($m,['inspections','schedule','documents','assets','supplies']),'head'=>in_array($m,['reports','inspections']),default=>false};}
public static function writable($m){return auth()->user()?->role==='hse'||(auth()->user()?->role==='inspector'&&$m==='inspections');}
private function audit($a,$d){DB::table('audit_logs')->insert(['user_id'=>auth()->id(),'action'=>$a,'detail'=>$d,'created_at'=>now(),'updated_at'=>now()]);}
public function login(Request $r){$r->merge(['email'=>mb_strtolower(trim((string)$r->input('email')))]);$v=$r->validate(['email'=>'required|email','password'=>'required']);if(!Auth::attempt($v))return back()->withErrors(['email'=>'Email atau kata sandi salah.'])->onlyInput('email');$r->session()->regenerate();return redirect()->intended('/dashboard');}
public function logout(Request $r){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect('/login');}
public function dashboard(Request $r){if(auth()->user()->role==='head')return redirect('/module/reports');if(auth()->user()->role==='inspector')return redirect('/module/inspections');$month=$r->input('month',date('Y-m'));abort_unless(preg_match('/^\d{4}-(0[1-9]|1[0-2])$/',$month),422);$entries=Entry::where('date','like',$month.'%')->get();$assets=Asset::all();$inspections=$entries->where('module','inspections');return view('dashboard',compact('entries','assets','inspections','month'));}
public function listing(Request $r,$module){abort_unless(self::allowed($module)&&isset(config('k3.modules')[$module]),403);$q=Entry::where('module',$module);if(auth()->user()->role==='head')$q->where('area',auth()->user()->area);foreach(['area','status'] as $f)if($r->filled($f))$q->where($f,$r->$f);if($r->filled('q'))$q->where('title','like','%'.$r->q.'%');return view('listing',['module'=>$module,'entries'=>$q->orderByDesc('date')->paginate(12)->withQueryString()]);}
public function form(Request $r,$module){abort_unless(self::writable($module)&&isset(config('k3.modules')[$module]),403);return view('form',['module'=>$module,'asset'=>$r->filled('asset')?Asset::findOrFail($r->asset):null,'assets'=>Asset::all(),'entry'=>null]);}
public function edit(Entry $entry){abort_unless(self::writable($entry->module)&&(auth()->user()->role==='hse'||$entry->user_id===auth()->id()),403);return view('form',['module'=>$entry->module,'asset'=>Asset::find($entry->asset_id),'assets'=>Asset::all(),'entry'=>$entry]);}
public function detail(Entry $entry){abort_unless(self::allowed($entry->module),403);if(auth()->user()->role==='head')abort_unless($entry->area===auth()->user()->area,403);return view('detail',compact('entry'));}
public function resolve(Request $r,Entry $entry){
if($entry->module==='schedule'){abort_unless(in_array(auth()->user()->role,['hse','inspector']),403);$v=$r->validate(['status'=>'required|in:Open,Selesai','resolution'=>'nullable|string|max:5000']);$entry->update($v);$this->audit('Tindak lanjut','Jadwal #'.$entry->id.' '.$v['status']);return back()->with('success',$v['status']==='Selesai'?'Jadwal ditandai selesai. Pengingat tidak muncul lagi.':'Jadwal dibuka kembali.');}
abort_unless(in_array($entry->module,['reports','inspections'])&&(auth()->user()->role==='hse'||(auth()->user()->role==='head'&&$entry->area===auth()->user()->area)),403);$v=$r->validate(['status'=>'required|in:Open,Process,Closed','resolution'=>'required|string|max:5000']);$entry->update($v);$this->audit('Tindak lanjut','Laporan #'.$entry->id.' '.$v['status']);return back()->with('success','Tindak lanjut tersimpan.');}
public function attachment(Entry $entry){$this->detail($entry);abort_unless($entry->attachment&&Storage::exists($entry->attachment),404);return Storage::download($entry->attachment);}

public function save(Request $r,$module,?Entry $entry=null){
$public=!auth()->check();abort_unless(($public&&$module==='reports')||self::writable($module),403);
if($entry)abort_unless($entry->module===$module&&(auth()->user()->role==='hse'||$entry->user_id===auth()->id()),403);
$v=$r->validate(['title'=>'required|string|max:180','area'=>['required',Rule::in(config('k3.areas'))],'category'=>['required',Rule::in(config('k3.categories.'.$module,[]))],'description'=>'required|string|max:5000','date'=>'required|date','asset_id'=>'nullable|exists:assets,id','reporter'=>'required|string|max:100','submission_key'=>'required|uuid','attachment'=>($module==='documents'&&!$entry?'required':'nullable').'|file|mimes:jpg,jpeg,png,pdf|max:5120']);
if(!$entry&&Entry::where('submission_key',$v['submission_key'])->exists())return back()->with('success','Data sudah tersimpan.');
unset($v['attachment']);$v['module']=$module;$v['status']=$entry?->status??($module==='documents'?'Aktif':($module==='inspections'?'Selesai':'Open'));$v['user_id']=$entry?->user_id??auth()->id();
if($module==='inspections'){$a=Asset::findOrFail($r->asset_id);$items=config('k3.checklists.'.$a->type);$checks=$r->input('checklist',[]);$result=[];foreach($items as $i=>$label){if(!isset($checks[$i])||!in_array($checks[$i],['Sesuai','Tidak sesuai']))return back()->withErrors(['checklist'=>'Lengkapi checklist.'])->withInput();$result[$label]=$checks[$i];}$v['checklist']=$result;$v['category']=in_array('Tidak sesuai',$result)?'Tidak sesuai':'Sesuai';$v['area']=$a->area;}
if($r->hasFile('attachment'))$v['attachment']=$r->file('attachment')->store('evidence');
DB::transaction(function()use($v,$entry,$module){$saved=$entry?tap($entry)->update($v):Entry::create($v);if($module==='inspections')Asset::where('id',$v['asset_id'])->update(['status'=>$v['category']==='Sesuai'?'Siap pakai':'Perlu tindakan']);$this->audit($entry?'Perubahan':'Penambahan',$module.' #'.$saved->id);});
return redirect($public?'/lapor':'/module/'.$module)->with('success','Data berhasil disimpan.');
}
public function assets(Request $r){abort_unless(self::allowed('assets'),403);$q=Asset::query();if($r->filled('q'))$q->where(fn($q)=>$q->where('name','like','%'.$r->q.'%')->orWhere('code','like','%'.$r->q.'%'));if($r->filled('type'))$q->where('type',$r->type);return view('assets',['assets'=>$q->orderBy('code')->paginate(15)->withQueryString()]);}
public function assetSave(Request $r){abort_unless(auth()->user()->role==='hse',403);$v=$r->validate(['code'=>'required|max:40|unique:assets','name'=>'required|max:120','type'=>['required',Rule::in(config('k3.types'))],'area'=>['required',Rule::in(config('k3.areas'))],'location'=>'required|max:180','due_date'=>'required|date']);$v['token']=(string)Str::uuid();Asset::create($v);$this->audit('Inventaris baru',$v['code']);return back()->with('success','Inventaris berhasil ditambahkan.');}
public function publicAsset($token){$asset=Asset::where('token',$token)->firstOrFail();return view('public-asset',['asset'=>$asset,'entries'=>Entry::where('asset_id',$asset->id)->where('module','inspections')->latest()->limit(8)->get(),'supplies'=>Supply::where('asset_id',$asset->id)->get()]);}
public function supplies(){abort_unless(self::allowed('supplies'),403);return view('supplies',['supplies'=>Supply::with('asset')->get(),'movements'=>DB::table('stock_movements')->join('supplies','supplies.id','=','stock_movements.supply_id')->select('stock_movements.*','supplies.name')->orderByDesc('stock_movements.id')->limit(20)->get()]);}
public function stock(Request $r,Supply $supply){$v=$r->validate(['quantity'=>'required|integer|min:1|max:10000','reporter'=>'required|string|max:100','purpose'=>'required|string|max:500','submission_key'=>'required|uuid','direction'=>'required|in:out,in']);if($v['direction']==='in')abort_unless(auth()->user()?->role==='hse',403);abort_unless($r->input('token')===$supply->asset->token||auth()->user()?->role==='hse',403);
DB::transaction(function()use($v,$supply){if(DB::table('stock_movements')->where('submission_key',$v['submission_key'])->exists())return;$s=Supply::lockForUpdate()->findOrFail($supply->id);$delta=$v['direction']==='in'?$v['quantity']:-$v['quantity'];if($delta<0&&($s->expiry<date('Y-m-d')||$s->stock<-$delta))throw \Illuminate\Validation\ValidationException::withMessages(['quantity'=>'Stok tidak cukup atau kedaluwarsa.']);if($delta<0){abort_unless(Supply::where('id',$s->id)->where('stock','>=',-$delta)->decrement('stock',-$delta),409);}else{$s->increment('stock',$delta);}DB::table('stock_movements')->insert(['supply_id'=>$s->id,'quantity'=>$delta,'reporter'=>$v['reporter'],'purpose'=>$v['purpose'],'submission_key'=>$v['submission_key'],'created_at'=>now(),'updated_at'=>now()]);});return back()->with('success','Mutasi persediaan berhasil dicatat.');}
public static function deletable(Entry $e){$u=auth()->user();return $u&&self::writable($e->module)&&($u->role==='hse'||$e->user_id===$u->id);}
public function destroy(Entry $entry){
abort_unless(self::deletable($entry),403);
$module=$entry->module;$file=$entry->attachment;$label=config('k3.modules.'.$module).' #'.$entry->id.' ('.Str::limit($entry->title,60).')';
DB::transaction(function()use($entry,$module,$label){
 $assetId=$entry->asset_id;$entry->delete();
 if($module==='inspections'&&$assetId){$last=Entry::where('module','inspections')->where('asset_id',$assetId)->orderByDesc('date')->orderByDesc('id')->first();Asset::where('id',$assetId)->update(['status'=>!$last||$last->category==='Sesuai'?'Siap pakai':'Perlu tindakan']);}
 $this->audit('Penghapusan',$label);
});
if($file&&Storage::exists($file))Storage::delete($file);
return redirect('/module/'.$module)->with('success','Data berhasil dihapus.');
}
public function assetDelete(Asset $asset){
abort_unless(auth()->user()->role==='hse',403);
DB::transaction(function()use($asset){
 $supplies=Supply::where('asset_id',$asset->id)->pluck('id');
 DB::table('stock_movements')->whereIn('supply_id',$supplies)->delete();
 Supply::whereIn('id',$supplies)->delete();
 Entry::where('asset_id',$asset->id)->update(['asset_id'=>null]);
 $asset->delete();
 $this->audit('Penghapusan','Inventaris '.$asset->code.' ('.$asset->name.')');
});
return redirect('/assets')->with('success','Inventaris '.$asset->code.' berhasil dihapus.');
}
public function supplyDelete(Supply $supply){
abort_unless(auth()->user()->role==='hse',403);
DB::transaction(function()use($supply){DB::table('stock_movements')->where('supply_id',$supply->id)->delete();$supply->delete();$this->audit('Penghapusan','Persediaan '.$supply->name);});
return redirect('/supplies')->with('success','Persediaan '.$supply->name.' berhasil dihapus.');
}
public function movementDelete($movement){
abort_unless(auth()->user()->role==='hse',403);
DB::transaction(function()use($movement){
 $m=DB::table('stock_movements')->lockForUpdate()->find($movement);abort_unless($m,404);
 $s=Supply::lockForUpdate()->findOrFail($m->supply_id);$next=$s->stock-$m->quantity;
 if($next<0)throw \Illuminate\Validation\ValidationException::withMessages(['movement'=>'Mutasi tidak dapat dihapus karena stok akan menjadi negatif.']);
 $s->update(['stock'=>$next]);DB::table('stock_movements')->where('id',$m->id)->delete();
 $this->audit('Penghapusan','Mutasi '.$s->name.' '.($m->quantity>0?'+':'').$m->quantity.' (stok dikoreksi menjadi '.$next.')');
});
return redirect('/supplies')->with('success','Mutasi dihapus dan stok sudah dikoreksi.');
}
public function auditLog(){abort_unless(auth()->user()->role==='hse',403);return view('audit',['logs'=>DB::table('audit_logs')->orderByDesc('id')->paginate(30)]);}
public function export($module){abort_unless(self::allowed($module)&&isset(config('k3.modules')[$module]),403);$q=Entry::where('module',$module);if(auth()->user()->role==='head')$q->where('area',auth()->user()->area);return response()->streamDownload(function()use($q){$f=fopen('php://output','w');fputcsv($f,['ID','Judul','Area','Kategori','Status','Tanggal']);foreach($q->cursor() as $e)fputcsv($f,array_map(fn($s)=>preg_match('/^[=+@\x2d]/',(string)$s)?"'".$s:$s,[$e->id,$e->title,$e->area,$e->category,$e->status,$e->date]));fclose($f);},$module.'.csv');}
}
