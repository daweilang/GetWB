<?php

namespace App\Libraries\Classes; 

/**
 * 获得微博数据基础接口封装
 * @author daweilang
 *
 */


abstract class GetWeiboHandler
{
	
	const VERSION = '1.0.1';
	
	/**
	 * 该条微博id
	 */
	public $mid;
	
	/**
	 * job执行延时
	 */
	public $delay = 5;
	
	/**
	 * 该条微博使用的model
	 * @var object
	 */
	protected $model = 'Weibo';

	/**
	 * 需要获取的该页面地址
	 * @var object
	 */
	protected $thisUrl;
	
	
	public function __construct()
	{
		/**
		 * 获得全局延时时间设置
		 */
		if(config('queue.delay')){
			$this->delay = config('queue.delay');
		}
	}
	
	
	/**
	 * 设置获得信息的任务
	 * @param string $jobName
	 */
	public function setJob($jobName=''){}
	
	
	/**
	 * 获得页面
	 * @param string $page 页面的页码
	 */
	public function getHtml($page){}
	
	
	/**
	 * 分析页面内容
	 * @param unknown $html
	 * @param string $file
	 */
	public function explainPage($html, $file =''){}
	
	
	/**
	 * 业务逻辑封装
	 * @param unknown $page
	 */
	public function process($page){
		$content = $this->getHtml($page);
		$this->explainPage($content);
	}
}