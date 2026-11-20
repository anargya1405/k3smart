<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\{User,Entry};
class AccountTest extends TestCase {
 use RefreshDatabase;
 protected function setUp():void {parent::setUp();$this->seed();}
 private function payload():array {return ['name'=>'Pengguna Baru','email'=>' BARU@example.test ','area'=>config('k3.areas')[0],'role'=>'inspector','password'=>'aman1234','password_confirmation'=>'aman1234'];}
 public function test_auth_pages_share_theme_and_registration_link():void {
  $this->get('/login')->assertOk()->assertSee('/register')->assertSee('/unified.css');
  $this->get('/register')->assertOk()->assertSee('password_confirmation')->assertSee('name="role"',false);
 }
 public function test_registration_is_active_immediately_and_hashes_password():void {
  $data=$this->payload();$this->post('/register',$data+['email_verified_at'=>now()])->assertRedirect('/dashboard');
  $user=User::where('email','baru@example.test')->firstOrFail();
  $this->assertSame('inspector',$user->role);$this->assertNull($user->email_verified_at);$this->assertTrue(Hash::check($data['password'],$user->password));$this->assertAuthenticatedAs($user);
  $this->get('/dashboard')->assertRedirect('/module/inspections');$this->get('/module/inspections')->assertOk();
  $this->get('/accounts')->assertForbidden();
 }
 public function test_registration_cannot_self_assign_hse():void {
  $data=$this->payload();$data['role']='hse';
  $this->post('/register',$data)->assertSessionHasErrors('role');$this->assertDatabaseMissing('users',['email'=>'baru@example.test']);$this->assertGuest();
 }
 public function test_invalid_registration_creates_no_user():void {
  $data=$this->payload();$data['password']='short';$data['password_confirmation']='different';$data['area']='Unknown';
  $this->post('/register',$data)->assertSessionHasErrors(['password','area']);$this->assertDatabaseCount('users',4);$this->assertGuest();
 }
 public function test_duplicate_email_is_rejected_case_insensitively():void {
  $data=$this->payload();$data['email']=' HSE@SAKTI.TEST ';
  $this->post('/register',$data)->assertSessionHasErrors('email');$this->assertDatabaseCount('users',4);
 }
 public function test_legacy_pending_account_is_activated_on_open():void {
  $user=User::factory()->create(['password'=>'AmanSekali2026']);$user->role='pending';$user->save();
  $this->actingAs($user)->get('/dashboard')->assertRedirect('/akun/menunggu');
  $this->get('/akun/menunggu')->assertRedirect('/dashboard');
  $this->assertSame('inspector',$user->fresh()->role);
 }
 public function test_hse_can_change_role_and_delete_account_with_audit():void {
  $hse=User::where('role','hse')->first();$user=User::where('role','inspector')->first();
  $this->actingAs($hse)->get('/accounts')->assertOk()->assertSee($user->email);
  $this->post('/accounts/'.$user->id,['role'=>'head','area'=>''])->assertSessionHasErrors('area');
  $this->post('/accounts/'.$user->id,['role'=>'head','area'=>'Gudang'])->assertRedirect('/accounts');
  $this->assertSame('head',$user->fresh()->role);$this->assertDatabaseHas('audit_logs',['action'=>'Perubahan akun']);
  $this->post('/accounts/'.$hse->id,['role'=>'inspector','area'=>''])->assertSessionHasErrors('role');
  $this->delete('/accounts/'.$hse->id)->assertSessionHasErrors('account');
  $this->delete('/accounts/'.$user->id)->assertRedirect('/accounts');$this->assertDatabaseMissing('users',['id'=>$user->id]);
 }
 public function test_other_roles_cannot_manage_accounts():void {
  $target=User::where('role','inspector')->first();
  foreach(['management','head','inspector'] as $role) {
   $this->actingAs(User::where('role',$role)->first())->get('/accounts')->assertForbidden();
   $this->post('/accounts/'.$target->id,['role'=>'hse','area'=>''])->assertForbidden();
   $this->delete('/accounts/'.$target->id)->assertForbidden();
  }
  $this->assertSame('inspector',$target->fresh()->role);
 }
 public function test_schedule_reminder_shows_in_web_and_can_be_marked_done():void {
  $hse=User::where('role','hse')->first();
  $e=Entry::create(['module'=>'schedule','title'=>'Cek APAR gudang terlambat','area'=>'Gudang','category'=>'Inspeksi bulanan','status'=>'Open','description'=>'Tes','date'=>now()->subDays(2)->toDateString(),'reporter'=>'HSE','submission_key'=>(string)Str::uuid()]);
  $this->actingAs($hse)->get('/assets')->assertOk()->assertSee('Cek APAR gudang terlambat')->assertSee('Terlambat 2 hari')->assertSee('schedule-toast',false);
  $this->get('/assets')->assertDontSee('schedule-toast',false);
  $this->post('/entry/'.$e->id.'/resolve',['status'=>'Selesai'])->assertSessionHasNoErrors();
  $this->assertSame('Selesai',$e->fresh()->status);
  $this->get('/assets')->assertDontSee('Cek APAR gudang terlambat');
  $this->actingAs(User::where('role','head')->first())->post('/entry/'.$e->id.'/resolve',['status'=>'Open'])->assertForbidden();
 }
 public function test_registration_is_rate_limited():void {
  for($i=0;$i<5;$i++)$this->post('/register',[])->assertSessionHasErrors('email');
  $this->post('/register',[])->assertStatus(429);
 }
}
