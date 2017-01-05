<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wb_like extends Model
{
	//
	public $timestamps = false;
	
// 	protected $primaryKey = ['uid', 'mid'];
	
	protected $fillable = ['mid', 'uid'];
	
	
	
}
