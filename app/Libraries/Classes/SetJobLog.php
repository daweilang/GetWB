<?php

namespace App\Libraries\Classes;

use App\Models\Wb_setjob_log;

/**
 * setjoblog基础类
 * 储存设置队列情况
 * @author daweilang
 *
 */

class SetJobLog
{
	
	const VERSION = '1.0.1';
	private $ob;
	
	public function __construct()
	{
	}
	
	
	/**
	 * 插入日志
	 **/
	public function createLog($array)
	{
		$this->ob = new Wb_setjob_log();
		foreach ($array as $k =>$v){
			$this->ob->$k = $v;
		}
		$this->ob->save();
		return $this->ob->id;
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