<?php

namespace App\Libraries\Classes;
use Illuminate\Support\Facades\Redis as Redis;

/**
 * 判断微博用户是否抓取的相关操作
 * @author daweilang
 *
 */


trait TraitWBUser
{	
	
	/**
	 * 判断redis是否可用
	 */
	public function redisConnect(){
		
	}
	
	/**
	 * 判断用户是否存在
	 * @param unknown $mid
	 */
	public function userExists($uid){
		if(config('database.uesRedisStorageUser')){
			$redis = Redis::connection("user");
			if($redis->exists("uid:".$uid)){
				return true;
			}
		}
		return \App\Models\Wb_user::firstOrNew(['uid'=>$uid]);
	}
	
	
	/**
	 * 插入用户到redis
	 * @param unknown $mid
	 */
	public function insertRedisUser($uid){
		if(config('database.uesRedisStorageUser')){
			$redis = Redis::connection("user");
			$redis->setnx("uid:".$uid, 0);
		}
		return ;
	}
}