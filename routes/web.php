<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\K3Controller as K;
Route::get('/login',fn()=>view('login'))->name('login')->middleware('guest');
Route::post('/login',[K::class,'login'])->middleware('throttle:5,1');
Route::get('/register',fn()=>view('register'))->middleware('guest')->name('register');
Route::post('/register',[\App\Http\Controllers\AccountController::class,'register'])->middleware(['guest','throttle:5,1']);
Route::get('/akun/menunggu',[\App\Http\Controllers\AccountController::class,'waiting'])->middleware('auth');
Route::get('/accounts',[\App\Http\Controllers\AccountController::class,'index'])->middleware('auth');
Route::post('/accounts/{user}/approve',[\App\Http\Controllers\AccountController::class,'approve'])->middleware(['auth','throttle:20,1']);
Route::post('/accounts/{user}',[\App\Http\Controllers\AccountController::class,'update'])->middleware(['auth','throttle:30,1']);
Route::delete('/accounts/{user}',[\App\Http\Controllers\AccountController::class,'reject'])->middleware(['auth','throttle:20,1']);
Route::post('/logout',[K::class,'logout'])->middleware('auth');
Route::get('/lapor',fn()=>view('public-report'));
Route::post('/lapor',fn(\Illuminate\Http\Request $r)=>(new K)->save($r,'reports'))->middleware('throttle:10,1');
Route::get('/q/{token}',[K::class,'publicAsset']);
Route::post('/stock/{supply}',[K::class,'stock'])->middleware('throttle:20,1');
Route::get('/', fn()=>view('landing'))->name('landing');
Route::middleware(['auth', \App\Http\Middleware\ActiveAccount::class])->group(function(){
 Route::get('/dashboard',[K::class,'dashboard'])->name('dashboard');Route::get('/assets',[K::class,'assets']);Route::post('/assets',[K::class,'assetSave']);Route::get('/supplies',[K::class,'supplies']);Route::get('/audit',[K::class,'auditLog']);
 Route::get('/module/{module}',[K::class,'listing']);Route::get('/module/{module}/new',[K::class,'form']);Route::post('/module/{module}',[K::class,'save']);Route::get('/entry/{entry}',[K::class,'detail']);Route::get('/entry/{entry}/edit',[K::class,'edit']);Route::post('/module/{module}/{entry}',[K::class,'save']);Route::post('/entry/{entry}/resolve',[K::class,'resolve']);Route::get('/entry/{entry}/attachment',[K::class,'attachment']);Route::get('/export/{module}',[K::class,'export']);
 Route::delete('/entry/{entry}',[K::class,'destroy']);Route::delete('/assets/{asset}',[K::class,'assetDelete']);Route::delete('/supplies/{supply}',[K::class,'supplyDelete']);Route::delete('/stock-movements/{movement}',[K::class,'movementDelete'])->whereNumber('movement');});

Route::get('/qr/{token}',function($token){$a=\App\Models\Asset::where('token',$token)->firstOrFail();return response(\App\Services\QrCode::svg(rtrim(config('app.url'),'/').'/q/'.$a->token),200,['Content-Type'=>'image/svg+xml']);});