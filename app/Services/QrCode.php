<?php
namespace App\Services;
/** QR Model 2, version 5, error correction L, byte mode, fixed mask 0. */
class QrCode {
 public static function svg(string $text): string {
 if(strlen($text)>106)throw new \InvalidArgumentException('URL QR maksimum 106 byte.');
 $bits='0100'.str_pad(decbin(strlen($text)),8,'0',STR_PAD_LEFT);
 foreach(unpack('C*',$text) as $b)$bits.=str_pad(decbin($b),8,'0',STR_PAD_LEFT);
 $bits.=str_repeat('0',min(4,864-strlen($bits)));while(strlen($bits)%8)$bits.='0';
 $data=[];foreach(str_split($bits,8) as $b)$data[]=bindec($b);
 for($i=0;count($data)<108;$i++)$data[]=$i%2?17:236;
 $mul=function($a,$b){$z=0;for($i=7;$i>=0;$i--){$z=($z<<1)^(($z>>7)*0x11D);$z^=(($b>>$i)&1)*$a;}return $z;};
 $div=array_fill(0,26,0);$div[25]=1;$root=1;
 for($i=0;$i<26;$i++){for($j=0;$j<26;$j++){$div[$j]=$mul($div[$j],$root);if($j<25)$div[$j]^=$div[$j+1];}$root=$mul($root,2);}
 $ecc=array_fill(0,26,0);foreach($data as $b){$factor=$b^array_shift($ecc);$ecc[]=0;for($j=0;$j<26;$j++)$ecc[$j]^=$mul($div[$j],$factor);}
 $stream='';foreach(array_merge($data,$ecc) as $b)$stream.=str_pad(decbin($b),8,'0',STR_PAD_LEFT);
 $n=37;$m=array_fill(0,$n,array_fill(0,$n,false));$f=$m;
 $set=function($x,$y,$v)use(&$m,&$f,$n){if($x>=0&&$y>=0&&$x<$n&&$y<$n){$m[$y][$x]=(bool)$v;$f[$y][$x]=true;}};
 for($i=0;$i<$n;$i++){$set(6,$i,$i%2===0);$set($i,6,$i%2===0);}
 foreach([[3,3],[33,3],[3,33]] as [$cx,$cy])for($dy=-4;$dy<=4;$dy++)for($dx=-4;$dx<=4;$dx++){$d=max(abs($dx),abs($dy));$set($cx+$dx,$cy+$dy,$d!==2&&$d!==4);}
 for($dy=-2;$dy<=2;$dy++)for($dx=-2;$dx<=2;$dx++)$set(30+$dx,30+$dy,max(abs($dx),abs($dy))!==1);
 $format=function($bits)use($set,$n){for($i=0;$i<=5;$i++)$set(8,$i,($bits>>$i)&1);$set(8,7,($bits>>6)&1);$set(8,8,($bits>>7)&1);$set(7,8,($bits>>8)&1);for($i=9;$i<15;$i++)$set(14-$i,8,($bits>>$i)&1);for($i=0;$i<8;$i++)$set($n-1-$i,8,($bits>>$i)&1);for($i=8;$i<15;$i++)$set(8,$n-15+$i,($bits>>$i)&1);$set(8,$n-8,true);};
 $rem=8;for($i=0;$i<10;$i++)$rem=($rem<<1)^(($rem>>9)*0x537);$format(((8<<10)|$rem)^0x5412);
 $index=0;
 for($right=$n-1;$right>=1;$right-=2){if($right===6)$right=5;for($vert=0;$vert<$n;$vert++){for($j=0;$j<2;$j++){$x=$right-$j;$y=(($right+1)&2)===0?$n-1-$vert:$vert;if(!$f[$y][$x]){$v=$index<strlen($stream)&&$stream[$index]==='1';$m[$y][$x]=$v^(($x+$y)%2===0);$index++;}}}}
 $path='';for($y=0;$y<$n;$y++)for($x=0;$x<$n;$x++)if($m[$y][$x])$path.='M'.($x+4).' '.($y+4).'h1v1h-1z';
 return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 45 45" shape-rendering="crispEdges"><rect width="100%" height="100%" fill="white"/><path fill="#102a33" d="'.$path.'"/></svg>';
 }
}