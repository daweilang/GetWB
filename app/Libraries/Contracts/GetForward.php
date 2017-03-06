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
use App\Models\Wb_user;

use App\Jobs\GetForwardContentJob;
use App\Libraries\Classes\WeiboContent;
use Symfony\Component\DomCrawler\Crawler;

use Storage;


class GetForward extends GetWeiboHandler
{	
	
	public function __construct($mid, $model='')
	{
		parent::__construct();
		
		$this->mid = $mid;
	
		//没有指定使用模型时，默认使用weibos表数据
		if($model){
			$this->model = $model;
		}
	}
	
	
	/**
	 * 根据评论页数，设置评论页队列任务
	 */
	public function setJob($page='1', $jobName='')
	{
		$model = "\App\Models\\$this->model";
		$weibo = $model::where('mid', $this->mid)->first();
		
		//插入监控表数据
		$Wb_forward_job = Wb_forward_job::create( [ 'mid' => $this->mid, 'j_page' => $page, 'model'=>$this->model]);
			
		//设置任务
		$this->setQueueClass("GetForwardContentJob", $Wb_forward_job, $jobName);
	}
	
	/**
	 * 抓取评论页面写入文件
	 */
	public function getHtml($page)
	{
		$this->getPage = $page;
		
		//评论页地址
		include app_path().'/Libraries/function/helpers.php';
		$this->thisUrl = sprintf(config('weibo.WeiboInfo.forwardUrl'), $this->mid, $page, dw_microtime());
		
		$file = "wbHtml/$this->mid/forward_$page";
		$errorFile = "wbHtml/$this->mid/error_forward_$page";
		
		$wb = new WeiboContent();
		//抓取
		$content = $wb->getWBHtml($this->thisUrl, config('weibo.CookieFile.weibo'), config('weibo.CookieFile.curl'));
		
		//获得微博返回的数组，同处理抓取异常
		$array = $this->getHtmlArray($content, $errorFile);
		
		$html = $array['data']['html'];
		
		Storage::put($file, $html);
		if(!Storage::exists($file)){
			throw new \Exception("无法储存微博转发页面");
		}
		return $html;
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
		
		//取设置任务时储存的博主mid
		//单独执行时有无法取得情况
		$model = "\App\Models\\$this->model";
		$weibo = $model::where('mid', $this->mid)->first();
		$oid = $weibo->uid;
		
		$page_total = 0;
		
		$crawler->filterXPath('//div[@class="list_li S_line1 clearfix"]')->each(function (Crawler $row) use ( $oid, &$page_total ){	
			
			//预定义匹配需要的变量
			$uid = $comment_pic_url = $created = '';
			
			//转发实质是用户发微博，所以标签是mid
			$wbForwardId = $row->filterXPath('//div[@class="list_li S_line1 clearfix"]')->filter('div')->attr('mid');
			
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
				$wbComment->mid = $this->mid;			
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
		});
		
		sleep(1);
		
		if($crawler->filterXPath('//div[@class="W_pages"]')->count()){
			
			if($this->getLastPage($crawler, $this->getPage+1)){
				$this->setJob($this->getPage+1, "");
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
}
