<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
	protected $fillable = ['nickname', 'email', 'website', 'content', 'article_id'];
	
	
	//
	public function hasOneArticle()
	{
// 		return $this->belongsTo('App\Models\article', 'article_id' , 'id');
		return $this->hasOne('App\Models\article', 'id', 'article_id');
	}
	
}
