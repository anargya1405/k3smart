<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{User,Asset,Entry,Supply};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class DeleteTest extends TestCase {
use RefreshDatabase;
protected function setUp():void{parent::setUp();$this->seed();}
private function user($role){return User::where('role',$role)->first();}

public function test_hse_can_delete_entry_from_every_module_and_it_is_audited():void{
$this->actingAs($this->user('hse'));
foreach(array_keys(config('k3.modules')) as $module){
 $e=Entry::where('module',$module)->first();
 if(!$e)continue;
 $this->delete('/entry/'.$e->id)->assertRedirect('/module/'.$module);
 $this->assertDatabaseMissing('records',['id'=>$e->id]);
}
$this->assertDatabaseHas('audit_logs',['action'=>'Penghapusan']);
}
public function test_delete_buttons_render_for_hse():void{
$this->actingAs($this->user('hse'));
$e=Entry::where('module','reports')->first();
$this->get('/entry/'.$e->id)->assertOk()->assertSee('data-confirm',false);
$this->get('/module/reports')->assertOk()->assertSee('/entry/'.$e->id,false)->assertSee('delete-form',false);
$this->get('/assets')->assertOk()->assertSee('delete-form',false);
$this->get('/supplies')->assertOk()->assertSee('delete-form',false);
}
public function test_management_and_head_cannot_delete():void{
$e=Entry::where('module','reports')->where('area','Produksi A')->first();
$this->actingAs($this->user('management'))->delete('/entry/'.$e->id)->assertForbidden();
$this->actingAs($this->user('head'))->delete('/entry/'.$e->id)->assertForbidden();
$this->delete('/assets/'.Asset::first()->id)->assertForbidden();
$this->assertDatabaseHas('records',['id'=>$e->id]);
}
public function test_guest_cannot_delete():void{
$e=Entry::first();$this->delete('/entry/'.$e->id)->assertRedirect('/login');$this->assertDatabaseHas('records',['id'=>$e->id]);
}
public function test_inspector_deletes_only_own_inspection_and_asset_status_recalculates():void{
$inspector=$this->user('inspector');$a=Asset::first();
$this->actingAs($inspector);
$other=Entry::where('module','inspections')->where('user_id','!=',$inspector->id)->first();
if($other)$this->delete('/entry/'.$other->id)->assertForbidden();
$d=['title'=>'Inspeksi salah input','reporter'=>'Inspektor','area'=>$a->area,'category'=>'Sesuai','description'=>'Salah','date'=>date('Y-m-d',strtotime('+1 day')),'submission_key'=>(string)Str::uuid(),'asset_id'=>$a->id,'checklist'=>array_fill(0,count(config('k3.checklists.'.$a->type)),'Tidak sesuai')];
$this->post('/module/inspections',$d)->assertSessionHasNoErrors();
$this->assertEquals('Perlu tindakan',$a->fresh()->status);
$own=Entry::where('title','Inspeksi salah input')->first();
$this->delete('/entry/'.$own->id)->assertRedirect('/module/inspections');
$last=Entry::where('module','inspections')->where('asset_id',$a->id)->orderByDesc('date')->orderByDesc('id')->first();
$this->assertEquals(!$last||$last->category==='Sesuai'?'Siap pakai':'Perlu tindakan',$a->fresh()->status);
}
public function test_hse_can_delete_asset_with_supplies_and_keeps_inspection_history():void{
$s=Supply::first();$a=$s->asset;$inspection=Entry::where('asset_id',$a->id)->first();
$this->actingAs($this->user('hse'))->delete('/assets/'.$a->id)->assertRedirect('/assets');
$this->assertDatabaseMissing('assets',['id'=>$a->id]);$this->assertDatabaseMissing('supplies',['id'=>$s->id]);
if($inspection)$this->assertDatabaseHas('records',['id'=>$inspection->id,'asset_id'=>null]);
$this->get('/q/'.$a->token)->assertNotFound();
}
public function test_deleting_movement_corrects_stock_and_never_goes_negative():void{
$s=Supply::first();$this->actingAs($this->user('hse'));
$this->post('/stock/'.$s->id,['quantity'=>5,'reporter'=>'HSE','purpose'=>'Salah input','direction'=>'in','submission_key'=>(string)Str::uuid()])->assertSessionHasNoErrors();
$after=$s->fresh()->stock;$m=DB::table('stock_movements')->where('purpose','Salah input')->first();
$this->delete('/stock-movements/'.$m->id)->assertRedirect('/supplies');
$this->assertEquals($after-5,$s->fresh()->stock);$this->assertDatabaseMissing('stock_movements',['id'=>$m->id]);
// a +N movement cannot be removed once the stock has already been used below N
$this->post('/stock/'.$s->id,['quantity'=>3,'reporter'=>'HSE','purpose'=>'Isi ulang','direction'=>'in','submission_key'=>(string)Str::uuid()]);
$in=DB::table('stock_movements')->where('purpose','Isi ulang')->first();
Supply::where('id',$s->id)->update(['stock'=>1]);
$this->from('/supplies')->delete('/stock-movements/'.$in->id)->assertSessionHasErrors('movement');
$this->assertEquals(1,$s->fresh()->stock);
}
public function test_hse_can_delete_supply():void{
$s=Supply::first();$this->actingAs($this->user('hse'))->delete('/supplies/'.$s->id)->assertRedirect('/supplies');$this->assertDatabaseMissing('supplies',['id'=>$s->id]);
}
public function test_hse_can_delete_accounts_but_not_itself():void{
$p=new User;$p->name='Calon';$p->email='calon@example.test';$p->password='RahasiaSekali123';$p->role='pending';$p->area='Gudang';$p->save();
$this->actingAs($this->user('hse'))->delete('/accounts/'.$p->id)->assertRedirect('/accounts');
$this->assertDatabaseMissing('users',['id'=>$p->id]);
$this->delete('/accounts/'.$this->user('hse')->id)->assertSessionHasErrors('account');$this->assertDatabaseHas('users',['role'=>'hse']);
}
}
