<?php

namespace App\Libraries\Classes;

/**
 * 封装获得需要的配置信息
 * @author daweilang
 *
 */


trait TraitGetConfig
{
	
	public function getQueueConf()
	{
		//获得全局延时时间设置
		if(empty($this->delay)){
			$this->delay = config('queue.delay');
		}
	}
}