<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wb_forward extends Model
{
    //
    protected $primaryKey = 'forward_id';
	protected $fillable = ['forward_id', 'mid'];
}
