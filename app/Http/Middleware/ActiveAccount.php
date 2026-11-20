<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class ActiveAccount {
 public function handle(Request $request,Closure $next) {
  if($request->user()?->role==='pending')return redirect('/akun/menunggu');
  return $next($request);
 }
}
