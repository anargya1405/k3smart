<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
 Schema::table('users', function(Blueprint $t){$t->string('role')->default('inspector');$t->string('area')->nullable();});
 Schema::create('assets',function(Blueprint $t){$t->id();$t->string('code')->unique();$t->uuid('token')->unique();$t->string('name');$t->string('type');$t->string('area');$t->string('location');$t->string('status')->default('Siap pakai');$t->date('due_date');$t->timestamps();});
 Schema::create('records',function(Blueprint $t){$t->id();$t->string('module')->index();$t->string('title');$t->string('area');$t->string('category');$t->string('status');$t->text('description');$t->date('date');$t->foreignId('asset_id')->nullable()->constrained();$t->foreignId('user_id')->nullable()->constrained();$t->string('reporter')->nullable();$t->string('attachment')->nullable();$t->json('checklist')->nullable();$t->text('resolution')->nullable();$t->uuid('submission_key')->unique();$t->timestamps();});
 Schema::create('supplies',function(Blueprint $t){$t->id();$t->foreignId('asset_id')->constrained();$t->string('name');$t->string('unit');$t->unsignedInteger('stock');$t->unsignedInteger('minimum');$t->date('expiry');$t->timestamps();});
 Schema::create('stock_movements',function(Blueprint $t){$t->id();$t->foreignId('supply_id')->constrained();$t->integer('quantity');$t->string('reporter');$t->text('purpose');$t->uuid('submission_key')->unique();$t->timestamps();});
 Schema::create('audit_logs',function(Blueprint $t){$t->id();$t->foreignId('user_id')->nullable()->constrained();$t->string('action');$t->text('detail');$t->timestamps();});
 }
 public function down(): void {foreach(['audit_logs','stock_movements','supplies','records','assets'] as $table) Schema::dropIfExists($table);Schema::table('users',fn(Blueprint $t)=>$t->dropColumn(['role','area']));}
};