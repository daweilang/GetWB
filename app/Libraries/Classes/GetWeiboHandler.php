<?php

namespace App\Libraries\Classes; 

use Log;
use Storage;
use Symfony\Component\DomCrawler\Crawler;
use App\Libraries\Classes\GetWBException;
use App\Libraries\Classes\WeiboContent;


/**
 * 获得微博数据封装抽象类
 * 该抽象类只提供抓取评论、赞、转发封装
 * @author daweilang
 *
 */

abstract class GetWeiboHandler
{
	
	const VERSION = '1.0.1';
	
	/**
	 * 该条微博id
	 */
	protected static $mid;

	/**
	 * 该条微博用户id
	 */
	protected static $uid;
	
	/**
	 * job执行延时
	 */
	public $delay = 5;
	
	/**
	 * 该条微博使用的model
	 * @var object
	 */
	protected static $model = 'Weibo';

	/**
	 * 需要获取的该页面地址
	 * @var object
	 */
	protected static $thisUrl;
	
	
	/**
	 * 设置抓取任务时抓取的页号
	 */
	protected static $getPage;

	
	/**
	 * 获取类型
	 */
	protected static $getType;
	
	
	/**
	 * 设置队列名
	 */
	protected static $jobName = '';

	
	/**
	 * 保存文件路径
	 */
	protected static $htmlFile = '';
	protected static $errorFile = '';
	
	
	public function __construct($mid, $model='')
	{
		//获得全局队列延时时间设置
		if(config('queue.delay')){
			$this->delay = config('queue.delay');
		}
		
		//该业务必须有mid
		static::$mid = $mid;
		
		//没有指定使用模型时，默认使用weibos表数据
		if($model){
			static::$model = $model;
		}
	}
	
	
	/**
	 * 设置获得信息的任务
	 * @param string $jobName
	 */
	abstract public function setJob($page='1');
	
	/**
	 * 分析页面内容
	 * @param unknown $html
	 * @param string $file
	 */
	abstract public function explainPage($html, $file ='');
	
	
	/**
	 * 设置抓取页面url
	 *
	 * @param unknown $mid
	 * @param string $page
	 *
	 * @return 返回需要抓取页面的地址
	 */
	protected static function setThisUrl($mid, $page){}
	
	
	/**
	 * 获得页面
	 * @param string $page 页面的页码
	 */
	public function getHtml($page)
	{
		//储存页面
		static::$getPage = $page;
	
		//获得微博信息
		$weibo = static::getWBInfo();
	
		//该条微博所属用户id
		static::$uid = $weibo->uid;
	
		//赞接口地址
		static::setThisUrl(static::$mid, $page);
		static::setThisFile();
		
		//获得页面内容，获得微博返回的数组，同处理抓取异常
		$array = static::getWBContent();
	
		$html = $array['data']['html'];
	
		//写入文件
		$this->storagePutFile($html);
	
		return $html;
	}
	
	/**
	 * 
	 * 业务流程使用方法
	 * 
	 */
	
	
	/**
	 * 插入任务日志，日志表结构完全一致
	 * 
	 * @return unknown
	 */
	protected static function insertSetJobPage($page)
	{
		//插入监控表数据
		$model = "\App\Models\\" . static::getJobPageModel();
		return $model::create( [ 'mid' => static::$mid, 'j_page' => $page, 'model'=> static::$model]);
	}
	
	
	/**
	 * 获得微博内容，需要子类将各参赛初始化
	 * @return mixed
	 */
	protected static function getWBContent()
	{
		$wb = new WeiboContent();
		//抓取
		$content = $wb->getWBHtml(static::$thisUrl, config('weibo.CookieFile.weibo'), config('weibo.CookieFile.curl'));
		//获得微博返回的数组，同处理抓取异常
		return static::getHtmlArray($content, static::$errorFile);
	}
	
	
	/**
	 * 封装queue执行逻辑
	 * 重跑数据也需要，所以为public
	 * @param unknown $className
	 * @param unknown $jobName
	 */
	public function setQueueClass($className, $classJob, $jobName='')
	{
		$class = "\App\Jobs\\$className";
		if(empty($jobName)){
			$job = (new $class($classJob))->delay($this->delay);
		}
		else{
			$job = (new $class($classJob))->onQueue($jobName)->delay($this->delay);
		}
		dispatch($job);
	}
	

	/**
	 * 如果继承没有实现这个方法报错
	 *
	 * @return string
	 *
	 * @throws \RuntimeException
	 */
	protected static function getJobPageModel()
	{
		throw new RuntimeException('GetWB does not implement getPageModel method.');
	}
	
	
	/**
	 * 获得微博接口返回的数组 
	 * 返回数组只decode不做处理
	 * 
	 * @param unknown $content
	 * @param unknown $errorFile
	 * 
	 * @throws \Exception
	 * @return mixed
	 */
	protected static function getHtmlArray($content, $errorFile)
	{
		$array = json_decode($content, true);
		if(!is_array($array) || $array['code'] !== '100000'){
			Storage::put($errorFile, $content);
			Log::error("无法获取接口数据", ['code'=>$array['code']]);
			throw new GetWBException("无法获取接口，请检查获取结果", 3001);
		}
		return $array;
	}
	
	
	/**
	 * 写入文件
	 * @param unknown $html
	 * @throws \Exception
	 */
	protected function storagePutFile($html)
	{
		//写入文件以便测试排错
		Storage::put(static::$htmlFile, $html);
		if(!Storage::exists(static::$htmlFile)){
			throw new \Exception("无法储存页面");
		}
	}
	
	/**
	 * 微博评论和赞接口页分页模式相同所以封装处理
	 * 判断是否有下一页，有下一页则返回true
	 * @param Crawler $crawler
	 * @param unknown $page
	 * @return boolean
	 */
	protected function getLastPage(Crawler $crawler , $page)
	{
		$last_page_text = $crawler->filterXPath('//div[@class="W_pages"]')->filterXPath('//span[contains(@action-type, "feed_list_page")]')->last()->text();
		$last_page_action = $crawler->filterXPath('//div[@class="W_pages"]')->filterXPath('//span[contains(@action-type, "feed_list_page")]')->last()->attr('action-data');
		$last_page = '';
		
		if(preg_match('/page=(\d+)/', $last_page_action, $m)){
			$last_page = $m[1];
		}
		
		if($last_page && $last_page==$page && $last_page_text=='下一页'){
			return true;
		}
		else{
			return false;	
		}
	}
	
	
	/**
	 * 获得微博信息封装
	 * @return unknown
	 */
	protected static function getWBInfo()
	{
		$model = "\App\Models\\". static::$model;
		return $model::where('mid', static::$mid)->first();
	}
	
	
	/**
	 * 设置保存文件路径
	 * @param unknown $type
	 */
	protected function setThisFile(){
		if(empty(static::$htmlFile)){
			static::$htmlFile = "wbHtml/".static::$uid."/".static::$mid."/".static::$getType."/page_".static::$getPage;
		}
		if(empty(static::$errorFile)){
			static::$errorFile = "wbHtml/".static::$uid."/".static::$mid."/".static::$getType."/error_".static::$getPage;
		}
	}
}
