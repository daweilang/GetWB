<?php

namespace App\Libraries\Classes;

use App\Models\Wb_user;

/**
 * 判断用户是否存在并插入数据
 * 使用mysql储存用户uid，当用户超过千万级别后使用redis储存uid，判断uid是否存在
 * @author daweilang
 *
 */

class SetUserInfo
{
	
	const VERSION = '1.0.1';
	private $ob;
	private $uid;
	
	
	public function __construct($uid)
	{
		$this->uid = $uid;
		//储存用户信息
		$this->ob = Wb_user::firstOrNew(['uid'=>$uid]);
	}
	
	
	/**
	 * 插入用户数据
	 **/
	public function createOrNew($array)
	{
		if(!$this->ob->exists){
			foreach ($array as $k =>$v){
				$this->ob->$k = $v;
			}
			$this->ob->save();
		}
		return $this->ob->uid;
	}

	
	/**
	 * 更新日志
	 **/
	public function updateLog($array, $id='')
	{
		//如果没有日志对象
		if(empty($this->ob->id)){
			$this->ob = Wb_setjob_log::find($id);
		}
		foreach ($array as $k =>$v){
			$this->ob->$k = $v;
		}
		$this->ob->save();
	}
}