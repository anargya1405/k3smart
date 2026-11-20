<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Entry extends Model {protected $guarded=[];protected $table='records'; protected $casts=['checklist'=>'array'];}