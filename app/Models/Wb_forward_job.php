<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wb_forward_job extends Model
{
    //
	protected $primaryKey = 'j_id';
	
	protected $fillable = ['mid', 'j_page', 'model'];
	
}
