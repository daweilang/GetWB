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
	 * 是否使用redis
	 * @var string
	 */
	private $_useRedis = config('database.uesRedisStorageUser');
	
	
	/**
	 * 判断redis是否可用
	 */
	public function redisConnect(){
// 		return $this->useRedis = false;
	}
	
	/**
	 * 判断用户是否存在
	 * @param unknown $mid
	 */
	public function userExists($uid){
		if($this->_useRedis){
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
		if($this->_useRedis){
			$redis = Redis::connection("user");
			$redis->setnx("uid:".$uid, 0);
		}
		return ;
	}
}