<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wb_complete_job extends Model
{
    //
	protected $primaryKey = 'j_id';
	
	protected $fillable = ['uid', 'j_complete_page'];
	
}
