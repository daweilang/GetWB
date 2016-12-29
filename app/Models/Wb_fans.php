<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wb_fans extends Model
{
	//
	public $timestamps = false;
	
// 	protected $primaryKey = ['uid', 'oid'];
	
	protected $fillable = ['uid', 'oid', 'status'];
	
	
	
}
