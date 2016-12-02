<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Weibo extends Model
{
	
// 	protected $fillable = ['nickname', 'email', 'website', 'content', 'article_id'];

	//
	public function hasManyComments()
	{
		return $this->hasMany('App\Models\Comment','wb_id', 'id');
	}
	
}
