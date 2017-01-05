<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wb_comment_job extends Model
{
    //
	protected $primaryKey = 'j_id';
	
	protected $fillable = ['mid', 'j_comment_page'];
	
}
