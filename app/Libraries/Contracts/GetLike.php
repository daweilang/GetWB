<?php

/**
 *  GetLike.php 抓取赞相关, 基础功能
 *  包括，设置任务 和 获取页面分析两种业务逻辑
 *  设计储存表结构一致，该类接收参数为表模型名称
 *  
 * @copyright			(C) daweilang
 * @license				https://github.com/daweilang/
 *
 */

namespace App\Libraries\Contracts;

use App\Libraries\Classes\GetWeiboHandler;

use App\Models\Wb_like;
use App\Models\Wb_like_job;
use App\Models\Wb_user;

use App\Jobs\GetLikeContentJob;
use App\Libraries\Classes\WeiboContent;
use Symfony\Component\DomCrawler\Crawler;

use Storage;


class GetLike extends GetWeiboHandler
{	
	
	/**
	 * 设置队列名
	 * @var string
	 */
	protected static $jobName = '';
	
	
	/**
	 * 获取类型
	 */
	protected static $getType = 'like';
	
	
	/**
	 * 本模块使用的pageModel
	 * @return string
	 */
	protected static function getJobPageModel()
	{
		return 'Wb_like_job';
	}
	
	
	/**
	 * 根据赞页数，设置评论页队列任务
	 */
	public function setJob($page='1')
	{
		//插入监控表数据
		$job_page = static::insertSetJobPage($page);
		//设置任务
		$this->setQueueClass("GetLikeContentJob", $job_page, static::$jobName);	
	}
	
	
	/**
	 * 获得评论的html分析
	 * @param $html 赞的html
	 * @param unknown $file 评论储存的html页面
	 */
	public function explainPage($html, $file ='')
	{
		if($file && Storage::exists($file)){
			//该页面应该是html
			$html = Storage::get($file);
		}
		
		$crawler = new Crawler();
		$crawler->addHtmlContent($html);
		
		//该微博id
		$oid = static::$uid;
		
		$page_total = 0;
		
		$crawler->filterXPath('//div[@class="WB_emotion"]')->filter('li')->each(function (Crawler $row) use ($oid, &$page_total) {
			
			$uid = $row->filter('li')->attr('uid');
			
			if($uid){
				
				//存储赞信息
				$like = Wb_like::firstOrNew(['mid' => static::$mid, 'uid'=>$uid]);
				//更新时不必改动项
				if(!$like->exists){
					$like->mid = static::$mid;
					$like->uid = $uid;
					$like->oid = $oid;
					$like->save();
				}
				
				//储存用户信息
				$wbUser = Wb_user::firstOrNew(['uid'=>$uid]);
				//后台执行抓取用户信息程序
				if(!$wbUser->exists){
					$wbUser->uid = $uid;
					$href = $row->filter('a')->attr('href');
					if(preg_match('/\/u\/(\d+)/', $href, $m)){
						$wbUser->usercard = $m[1];
					}
					else{
						$wbUser->usercard = ltrim($href, "\/");
					}				
					$wbUser->username = $row->filter('a>img')->attr('title');
					$wbUser->photo_url = $row->filter('a>img')->attr('src');
					$wbUser->save();
				}		

				$page_total++;
			}
		});
		
		sleep(1);
		
		if($crawler->filterXPath('//div[@class="W_pages"]')->count()){
			
			if($this->getLastPage($crawler, (static::$getPage)+1)){
				$this->setJob((static::$getPage)+1);
			}
			else{
				//没有最后一页是尾页，停止设置抓取
				//由于weibo任务是赞和转发，评论等多队列，所以无法判断单个状态
				//可以设计添加多个状态
	// 			$weibo->status=4;
	// 			$weibo->save();
			}
		}
		
		return $page_total;
	}
	
	
	/**
	 * 获得赞接口地址
	 * {@inheritDoc}
	 * @see \App\Libraries\Classes\GetWeiboHandler::getThisUrl()
	 */
	public function setThisUrl($mid, $page){
		if(empty(static::$thisUrl)){
			static::$thisUrl = sprintf(config('weibo.WeiboInfo.likeUrl'), $mid, $page);
		}
	}
	
}
