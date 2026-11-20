<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,DB};
use Illuminate\Validation\{Rule,Rules\Password};
class AccountController extends Controller {
 /** Peran yang boleh dipilih sendiri saat mendaftar. HSE (administrator) tidak bisa dipilih sendiri. */
 public const SELF_ROLES=['inspector'=>'Inspektor — pemeriksaan lapangan','head'=>'Head Area — tindak lanjut area','management'=>'Top Management — pemantauan'];
 public const ROLES=['hse'=>'HSE — administrator','inspector'=>'Inspektor','head'=>'Head Area','management'=>'Top Management'];

 private function audit($action,$detail){DB::table('audit_logs')->insert(['user_id'=>auth()->id(),'action'=>$action,'detail'=>$detail,'created_at'=>now(),'updated_at'=>now()]);}

 public function register(Request $request) {
  $request->merge(['email'=>mb_strtolower(trim((string)$request->input('email')))]);
  $data=$request->validate(['name'=>'required|string|max:100','email'=>'required|email|max:255|unique:users,email','area'=>['required',Rule::in(config('k3.areas'))],'role'=>['required',Rule::in(array_keys(self::SELF_ROLES))],'password'=>['required','confirmed',Password::min(8)]],['password.confirmed'=>'Konfirmasi kata sandi belum cocok.','email.unique'=>'Email sudah terdaftar. Silakan masuk.','role.in'=>'Pilih peran yang tersedia.']);
  $user=DB::transaction(function()use($data){$user=new User;$user->name=$data['name'];$user->email=$data['email'];$user->password=$data['password'];$user->area=$data['area'];$user->role=$data['role'];$user->save();DB::table('audit_logs')->insert(['user_id'=>$user->id,'action'=>'Akun baru','detail'=>'Akun '.$user->email.' terdaftar sebagai '.$user->role,'created_at'=>now(),'updated_at'=>now()]);return $user;});
  Auth::login($user);$request->session()->regenerate();
  return redirect('/dashboard')->with('success','Akun berhasil dibuat. Selamat datang di SMART K3, '.$user->name.'.');
 }
 /** Akun lama yang masih berstatus menunggu diaktifkan sebagai inspektor saat dibuka. */
 public function waiting() {
  $user=auth()->user();
  if($user->role==='pending'){$user->role='inspector';$user->save();}
  return redirect('/dashboard');
 }
 public function index() {abort_unless(auth()->user()->role==='hse',403);User::where('role','pending')->update(['role'=>'inspector']);return view('accounts',['accounts'=>User::orderByRaw("case role when 'hse' then 0 when 'management' then 1 when 'head' then 2 else 3 end")->orderBy('name')->paginate(20)]);}
 public function update(Request $request,User $user) {
  abort_unless(auth()->user()->role==='hse',403);
  $data=$request->validate(['role'=>['required',Rule::in(array_keys(self::ROLES))],'area'=>['nullable',Rule::in(config('k3.areas'))]]);
  if($user->id===auth()->id()&&$data['role']!=='hse')return back()->withErrors(['role'=>'Anda tidak dapat menurunkan peran akun Anda sendiri.']);
  if($data['role']==='head'&&empty($data['area']))return back()->withErrors(['area'=>'Head Area wajib memiliki area kerja.']);
  $before=$user->role;$user->role=$data['role'];$user->area=$data['area']?:null;$user->save();
  $this->audit('Perubahan akun','Akun '.$user->email.': '.$before.' → '.$user->role.($user->area?' ('.$user->area.')':''));
  return redirect('/accounts')->with('success','Akses '.$user->name.' diperbarui.');
 }
 public function approve(Request $request,User $user) {return $this->update($request,$user);}
 public function reject(User $user) {
  abort_unless(auth()->user()->role==='hse',403);
  if($user->id===auth()->id())return back()->withErrors(['account'=>'Akun yang sedang dipakai tidak dapat dihapus.']);
  if($user->role==='hse'&&User::where('role','hse')->count()<=1)return back()->withErrors(['account'=>'Minimal harus ada satu akun HSE.']);
  DB::transaction(function()use($user){
   DB::table('records')->where('user_id',$user->id)->update(['user_id'=>null]);
   DB::table('audit_logs')->where('user_id',$user->id)->update(['user_id'=>null]);
   $this->audit('Penghapusan','Akun '.$user->email.' ('.$user->role.') dihapus');
   $user->delete();
  });
  return redirect('/accounts')->with('success','Akun dihapus. Data yang pernah dibuat akun ini tetap tersimpan.');
 }
}
