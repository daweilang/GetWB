<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wb_comment extends Model
{
    //
    protected $primaryKey = 'comment_id';
	protected $fillable = ['comment_id', 'mid'];
}
