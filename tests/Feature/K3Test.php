<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{User,Asset,Entry,Supply};
use Illuminate\Support\Str;
class K3Test extends TestCase {
use RefreshDatabase;
protected function setUp():void{parent::setUp();$this->seed();}
private function user($role){return User::where('role',$role)->first();}
public function test_guests_require_login_and_public_pages_render():void{
$this->get('/')->assertOk()->assertSee('Setiap langkah');$this->get('/dashboard')->assertRedirect('/login');$this->get('/login')->assertOk();$this->get('/lapor')->assertOk();$this->get('/q/'.Asset::first()->token)->assertOk();
}
public function test_hse_pages_and_forms_render():void{
$this->actingAs($this->user('hse'));
foreach(['/dashboard','/assets','/supplies','/audit','/module/reports','/module/inspections','/module/schedule','/module/documents','/module/incidents','/module/reports/new','/module/documents/new','/module/inspections/new?asset=1','/entry/1','/entry/1/edit'] as $url)$this->get($url)->assertOk();
}
public function test_management_cannot_write():void{
$this->actingAs($this->user('management'))->post('/assets',[])->assertForbidden();
$this->post('/module/reports',[])->assertForbidden();
$this->get('/module/reports/new')->assertForbidden();
}
public function test_area_access_is_enforced():void{
$outside=Entry::where('module','reports')->where('area','!=','Produksi A')->first();
$this->actingAs($this->user('head'))->get('/entry/'.$outside->id)->assertForbidden();
$this->post('/entry/'.$outside->id.'/resolve',['status'=>'Closed','resolution'=>'test'])->assertForbidden();
$this->get('/module/reports')->assertOk()->assertDontSee($outside->title);
}
public function test_public_report_is_idempotent():void{
$data=['title'=>'Temuan tes','reporter'=>'Penguji','area'=>'Gudang','category'=>'Unsafe condition','description'=>'Material menghalangi akses','date'=>date('Y-m-d'),'submission_key'=>(string)Str::uuid()];
$this->post('/lapor',$data)->assertRedirect('/lapor');$this->post('/lapor',$data)->assertRedirect();
$this->assertEquals(1,Entry::where('title','Temuan tes')->count());
}
public function test_stock_cannot_go_negative_or_duplicate():void{
$s=Supply::first();$stock=$s->stock;
$d=['quantity'=>$stock+1,'reporter'=>'Tes','purpose'=>'Pengujian','direction'=>'out','token'=>$s->asset->token,'submission_key'=>(string)Str::uuid()];
$this->post('/stock/'.$s->id,$d)->assertSessionHasErrors('quantity');
$d['quantity']=2;$this->post('/stock/'.$s->id,$d)->assertSessionHasNoErrors();$this->post('/stock/'.$s->id,$d);
$this->assertEquals($stock-2,$s->fresh()->stock);
}
public function test_public_cannot_restock():void{
$s=Supply::first();$this->post('/stock/'.$s->id,['quantity'=>1,'reporter'=>'Tes','purpose'=>'Tes','direction'=>'in','token'=>$s->asset->token,'submission_key'=>(string)Str::uuid()])->assertForbidden();
}
public function test_inspection_updates_asset_and_creates_audit():void{
$a=Asset::first();$d=['title'=>'Inspeksi pengujian','reporter'=>'Inspektor','area'=>$a->area,'category'=>'Sesuai','description'=>'Pengujian checklist','date'=>date('Y-m-d'),'submission_key'=>(string)Str::uuid(),'asset_id'=>$a->id,'checklist'=>array_fill(0,count(config('k3.checklists.'.$a->type)),'Sesuai')];$d['checklist'][0]='Tidak sesuai';
$this->actingAs($this->user('inspector'))->post('/module/inspections',$d)->assertSessionHasNoErrors()->assertRedirect('/module/inspections');
$this->assertEquals('Perlu tindakan',$a->fresh()->status);$this->assertDatabaseHas('records',['title'=>'Inspeksi pengujian','category'=>'Tidak sesuai']);$this->assertDatabaseHas('audit_logs',['action'=>'Penambahan']);
}
public function test_inspector_cannot_edit_others_inspection():void{
$this->actingAs($this->user('inspector'))->get('/entry/1/edit')->assertForbidden();
}
public function test_login_and_logout():void{
$this->post('/login',['email'=>'hse@sakti.test','password'=>'SaktiDemo!2026'])->assertRedirect('/dashboard');$this->assertAuthenticated();$this->post('/logout')->assertRedirect('/login');$this->assertGuest();
}

public function test_hse_can_reduce_stock_without_public_token_and_reject_overdraft():void{
$s=Supply::first();$original=$s->stock;
$this->actingAs($this->user('hse'));
$data=['quantity'=>3,'reporter'=>$this->user('hse')->name,'purpose'=>'Pengambilan P3K oleh HSE','direction'=>'out','submission_key'=>(string)Str::uuid()];
$this->post('/stock/'.$s->id,$data)->assertSessionHasNoErrors();
$this->assertEquals($original-3,$s->fresh()->stock);
$this->assertDatabaseHas('stock_movements',['supply_id'=>$s->id,'quantity'=>-3,'purpose'=>$data['purpose']]);
$data['quantity']=$original;$data['submission_key']=(string)Str::uuid();
$this->post('/stock/'.$s->id,$data)->assertSessionHasErrors('quantity');
$this->assertEquals($original-3,$s->fresh()->stock);
$this->assertDatabaseCount('stock_movements',1);
}
}
