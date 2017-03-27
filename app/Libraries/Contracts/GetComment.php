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

use App\Models\Wb_comment_job;
use App\Models\Wb_comment;
use App\Models\Wb_user;

use App\Jobs\GetCommentContentJob;
use App\Libraries\Classes\WeiboContent;
use Symfony\Component\DomCrawler\Crawler;

use Storage;


class GetComment extends GetWeiboHandler
{	
	

	/**
	 * 设置队列名
	 * @var string
	 */
	protected static $jobName = '';
	
	
	/**
	 * 获取类型
	 */
	protected static $getType = 'comment';
	
	
	/**
	 * 本模块使用的pageModel
	 * @return string
	 */
	public static function getJobPageModel()
	{
		return 'Wb_comment_job';
	}
	
	/**
	 * 根据评论页数，设置评论页队列任务
	 */
	public function setJob($page='1')
	{	
		//插入监控表数据
		$job_page = static::insertSetJobPage($page);
		//设置任务
		$this->setQueueClass("GetCommentContentJob", $job_page, static::$jobName);
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
			
			//评论id
			$wbCommentId = $row->filterXPath('//div[@class="list_li S_line1 clearfix"]')->filter('div')->attr('comment_id');
			
			if($wbCommentId){
			
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
				$wbComment = Wb_comment::firstOrNew(['comment_id'=>$wbCommentId]);
				if(!$wbComment->exists){
					$wbComment->comment_id = $wbCommentId;
					$wbComment->mid = static::$mid;			
				}
				$wbComment->uid = $uid;
				$wbComment->oid = $oid;
				$wbComment->username = $username;
				$wbComment->usercard = $usercard;
				$wbComment->content = $content;
				$wbComment->comment_pic_url = $comment_pic_url;
				$wbComment->wb_created = $created;
				$wbComment->save();
					
				if($uid){	
					//储存用户信息
					$wbUser = Wb_user::firstOrNew(['uid'=>$uid]);
					//后台执行抓取用户信息程序
					if(!$wbUser->exists){
						$wbUser->uid = $uid;
						$wbUser->username = $username;
						$wbUser->usercard = $usercard;
						$wbUser->save();
					}	
				}
				
				$page_total++;
			}
			else{
				Log::error("数据接口异常", ['url'=>static::$thisUrl]);
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
		
		return $page_total;
		
	}
	
	
	public function setThisUrl($mid, $page){
		//评论页地址
		if(empty(static::$thisUrl)){
			static::$thisUrl = sprintf(config('weibo.WeiboInfo.commentUrl'), static::$mid, $page);
		}
	}
}
