<?php

namespace App\Libraries\Classes;

/**
 * 判断用户是否存在并插入数据
 * 使用mysql储存用户uid，当用户超过千万级别后使用redis储存uid，判断uid是否存在
 * @author daweilang
 *
 */

class SetUserInfo
{
	
	const VERSION = '1.0.1';
	private $uid;
	
	public function __construct($uid)
	{
		$this->uid = $uid;
		$this->userExists();
	}
	
	
	public function userExists(){
		return false;
	}

}