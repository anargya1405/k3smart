<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{User,Asset,Entry,Supply};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class DatabaseSeeder extends Seeder{
public function run(): void{
foreach(['hse'=>'Anargya · HSE','management'=>'Manajemen','inspector'=>'Petugas Inspeksi','head'=>'Kepala Produksi A'] as $role=>$name){$u=User::firstOrNew(['email'=>$role.'@sakti.test']);$u->name=$name;$u->password=Hash::make('SaktiDemo!2026');$u->role=$role;$u->area=$role==='head'?'Produksi A':null;$u->save();}
$admin=User::where('role','hse')->first();
foreach(config('k3.types') as $i=>$type){for($j=1;$j<=3;$j++){$code=['APAR','APAB','HYD','SC','EXIT','P3K','ROOM'][$i].'-'.str_pad($j,3,'0',STR_PAD_LEFT);$a=Asset::firstOrCreate(['code'=>$code],['token'=>(string)Str::uuid(),'name'=>$type.' '.$j,'type'=>$type,'area'=>config('k3.areas')[($i+$j)%5],'location'=>['Dekat pintu masuk','Sisi timur bangunan','Koridor utama'][$j-1],'status'=>($i===0&&$j===2)?'Perlu tindakan':'Siap pakai','due_date'=>now()->addDays($j*4)->toDateString()]);
if($j<3)Entry::firstOrCreate(['title'=>'Inspeksi '.$code,'module'=>'inspections'],['area'=>$a->area,'category'=>$a->status==='Siap pakai'?'Sesuai':'Tidak sesuai','status'=>'Selesai','description'=>'Data contoh untuk demonstrasi penelitian.','date'=>now()->startOfMonth()->addDays($j)->toDateString(),'asset_id'=>$a->id,'user_id'=>$admin->id,'reporter'=>'Petugas Inspeksi','submission_key'=>(string)Str::uuid()]);
if($type==='Kotak P3K')foreach(['Kasa steril','Plester luka','Sarung tangan'] as $k=>$name)Supply::firstOrCreate(['asset_id'=>$a->id,'name'=>$name],['unit'=>'pcs','stock'=>[24,8,30][$k],'minimum'=>10,'expiry'=>now()->addMonths(8)->toDateString()]);
}}
foreach([['Palet menghalangi jalur evakuasi','Gudang','Unsafe condition','Open'],['Pelindung mesin perlu perbaikan','Produksi A','Unsafe condition','Process'],['Penggunaan APD belum lengkap','Produksi B','Unsafe action','Closed'],['Lantai licin dekat akses utility','Utility','Unsafe condition','Open'],['Penyimpanan material sudah ditata','Gudang','Unsafe condition','Closed']] as $i=>$d)Entry::firstOrCreate(['module'=>'reports','title'=>$d[0]],['area'=>$d[1],'category'=>$d[2],'status'=>$d[3],'description'=>'Simulasi penelitian: '.$d[0].'. Periksa lokasi bersama penanggung jawab area.','date'=>now()->startOfMonth()->addDays($i+1)->toDateString(),'user_id'=>$admin->id,'reporter'=>'Tim HSE','submission_key'=>(string)Str::uuid()]);
foreach(['Pemeriksaan APAR bulanan','Pemeriksaan fasilitas P3K','Inspeksi jalur evakuasi'] as $i=>$title)Entry::firstOrCreate(['module'=>'schedule','title'=>$title],['area'=>config('k3.areas')[$i],'category'=>'Inspeksi bulanan','status'=>'Open','description'=>'Agenda contoh penelitian.','date'=>now()->startOfMonth()->addDays(18+$i*3)->toDateString(),'user_id'=>$admin->id,'reporter'=>'Petugas Inspeksi','submission_key'=>(string)Str::uuid()]);
Entry::firstOrCreate(['module'=>'incidents','title'=>'Simulasi near miss di koridor gudang'],['area'=>'Gudang','category'=>'Nearmiss','status'=>'Open','description'=>'Data simulasi, bukan kejadian perusahaan.','date'=>now()->toDateString(),'user_id'=>$admin->id,'reporter'=>'Tim HSE','submission_key'=>(string)Str::uuid()]);
}}