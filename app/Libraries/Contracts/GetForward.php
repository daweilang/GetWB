<?php

/**
 *  GetComment.php 抓取评论相关
 *  该程序为旧接口，评论和评论回复一起输出，没有父子关系
 * @copyright			(C) daweilang
 * @license				https://github.com/daweilang/
 *
 */


namespace App\Libraries\Contracts;

use App\Libraries\Classes\GetWeiboHandler;

use App\Models\Wb_forward_job;
use App\Models\Wb_forward;

use App\Jobs\GetForwardContentJob;
use App\Libraries\Classes\GetWBException;
use Symfony\Component\DomCrawler\Crawler;

use App\Libraries\Classes\TraitWBUser;

use Storage;
use Log;


class GetForward extends GetWeiboHandler
{

	use TraitWBUser;
	
	/**
	 * 设置队列名
	 * @var string
	 */
	protected static $jobName = '';
	
	
	/**
	 * 获取类型
	 */
	protected static $getType = 'forward';
	
	/**
	 * 本模块使用的pageModel
	 * @return string
	 */
	public static function getJobPageModel()
	{
		return 'Wb_forward_job';
	}
	
	/**
	 * 根据评论页数，设置评论页队列任务
	 */
	public function setJob($page='1')
	{	
		//插入监控表数据
		$job_page = static::insertSetJobPage($page);
		//设置任务
		$this->setQueueClass("GetForwardContentJob", $job_page, static::$jobName);
	}
	
	
	/**
	 * 获得评论的html分析
	 * @param $commentHtml 评论的html
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
		
		$oid = static::$uid;
		
		$page_total = 0;
		
		$crawler->filterXPath('//div[@class="list_li S_line1 clearfix"]')->each(function (Crawler $row) use ( $oid, &$page_total ){	
			
			//预定义匹配需要的变量
			$uid = $comment_pic_url = $created = '';
			
			//转发实质是用户发微博，所以标签是mid
			$wbForwardId = $row->filterXPath('//div[@class="list_li S_line1 clearfix"]')->filter('div')->attr('mid');
			
			if($wbForwardId){
				
				//评论者的主页
				//根据链接获得用户的usercard
				$wbface = $row->filterXPath('//div[@class="WB_text"]')->filter('a')->attr('href');
				$usercard = ltrim($wbface, "\/");
				
				//评论者的id
				//链接的usercard标签储存的是uid
				$imgUsercard = $row->filterXPath('//div[@class="WB_text"]')->filter('a')->attr('usercard');
				if(preg_match('/id\=(\d+)/', $imgUsercard , $m)){
					$uid = $m[1];
				}
				
				//评论者的name
				$username = $row->filterXPath('//div[@class="WB_text"]')->filter('a')->text();
				
				//评论内容
				$text = trim($row->filterXPath('//div[@class="WB_text"]')->text());
				$content = mb_substr($text,mb_strlen($username."：",'UTF-8'), null, 'UTF-8');
				
				//评论内容配图
				if($row->filterXPath('//div[@class="WB_media_wrap clearfix"]')->count()){
					$comment_pic_url = $row->filterXPath('//div[@class="media_box"]')->filter('ul>li>img')->attr('src');
				}
				
				$timeText = $row->filterXPath('//div[@class="WB_from S_txt2"]')->text();
				if(preg_match('/(\d{4}\-\d{1,2}\-\d{1,2}\s+\d{2}\:\d{2}:\d{2})/', $timeText, $m)){
					$created = $m[1];
				}
				elseif(preg_match('/(\d{4}\-\d{1,2}\-\d{1,2}\s+\d{2}\:\d{2})/', $timeText, $m))
				{
					$created = $m[1];
				}
				elseif(preg_match('/(\d{1,2})月(\d{1,2})日\s+(\d{1,2})\:(\d{1,2})/', $timeText, $m))
				{
					$created = sprintf("%04d-%02d-%02d %02d:%02d:00", date("Y"), $m[1], $m[2], $m[3], $m[4]);
				}
				elseif(preg_match('/今天\s+(\d{1,2})\:(\d{1,2})/', $timeText, $m))
				{
					$created = sprintf("%04d-%02d-%02d %02d:%02d:00", date("Y"), date("m"), date("d"), $m[1], $m[2]);
				}
				elseif(preg_match('/(\d+)分钟前/', $timeText, $m)){
					$created = date("Y-m-d H:i:00", time()-$m[1]*60);
				}
				
			
				//更新时不必改动项
				$wbComment = Wb_forward::firstOrNew(['forward_id'=>$wbForwardId]);
				if(!$wbComment->exists){
					$wbComment->forward_id = $wbForwardId;
					$wbComment->mid = static::$mid;			
				}
				$wbComment->uid = $uid;
				$wbComment->oid = $oid;
				$wbComment->username = $username;
				$wbComment->usercard = $usercard;
				$wbComment->content = $content;
				$wbComment->forward_pic_url = $comment_pic_url;
				$wbComment->wb_created = $created;
				$wbComment->save();
					
				if($uid){	
					//储存用户信息
					$wbUser = $this->userExists($uid);
					//后台执行抓取用户信息程序
					if(is_object($wbUser)){
						$wbUser->uid = $uid;
						$wbUser->username = $username;
						$wbUser->usercard = $usercard;
						$wbUser->save();
						$this->insertRedisUser($uid);
					}	
				}
				
				$page_total++;
			}			
			else{
				Log::error("数据接口异常，没有数据", ['url'=>static::$thisUrl]);
				throw new GetWBException("数据接口异常", 3002);
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
		else{
			//第一页没有分页
			if(static::$getPage !=1 ){
				Log::error("数据接口异常, 没有分页", ['url'=>static::$thisUrl]);
				throw new GetWBException("数据接口异常, 没有分页", 3004);
			}
		}
		return $page_total;
		
	}
	
	/**
	 * 获得转发页面接口地址
	 * {@inheritDoc}
	 * @see \App\Libraries\Classes\GetWeiboHandler::setThisUrl()
	 */
	public static function setThisUrl($mid, $page){
		if(empty(static::$thisUrl)){
			//评论页地址
			include app_path().'/Libraries/function/helpers.php';
			static::$thisUrl = sprintf(config('weibo.WeiboInfo.forwardUrl'), $mid, $page, dw_microtime());
		}
	}
}
