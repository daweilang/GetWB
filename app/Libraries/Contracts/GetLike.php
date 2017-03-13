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
	
	public function __construct($mid, $model='')
	{
		
		parent::__construct();
		
		//评论、赞、转发接口格式固定，只需要mid即可，不必传递微博信息
		$this->mid = $mid;	
		
		//没有指定使用模型时，默认使用weibos表数据
		if($model){
			$this->model = $model;
		}
	}
	
	
	/**
	 * 根据赞页数，设置评论页队列任务
	 */
	public function setJob($page='1', $jobName='')
	{
		$model = "\App\Models\\$this->model";
		$weibo = $model::where('mid', $this->mid)->first();
			
		//like任务表登记任务，表储存了需要抓取数据的来源'model'
		$like_job = Wb_like_job::create( [ 'mid' => $this->mid, 'j_like_page' => $page, 'model'=>$this->model]);
		
		//设置任务
		$this->setQueueClass("GetLikeContentJob", $like_job, $jobName);
		
	}
	
	/**
	 * 抓取赞接口页面写入文件
	 */
	public function getHtml($page)
	{
		$model = "\App\Models\\$this->model";
		$weibo = $model::where('mid', $this->mid)->first();
		//该条微博的id
		$this->uid = $weibo->uid;
		
		$this->getPage = $page;
		
		//赞接口地址
		$this->thisUrl = sprintf(config('weibo.WeiboInfo.likeUrl'), $this->mid, $page);
		
		$file = "wbHtml/$this->uid/$this->mid/like_$page";
		$errorFile = "wbHtml/$this->uid/$this->mid/error_like_$page";
		
		$wb = new WeiboContent();
		//抓取
		$content = $wb->getWBHtml($this->thisUrl, config('weibo.CookieFile.weibo'), config('weibo.CookieFile.curl'));

		//获得微博返回的数组，同处理抓取异常
		$array = $this->getHtmlArray($content, $errorFile);
		
		$html = $array['data']['html'];
		
		//写入文件以便测试排错
		Storage::put($file, $html);
		if(!Storage::exists($file)){
			throw new \Exception("无法储存页面");
		}
		return $html;
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
		$oid = $this->uid;
		
		$page_total = 0;
		
		$crawler->filterXPath('//div[@class="WB_emotion"]')->filter('li')->each(function (Crawler $row) use ($oid, &$page_total) {
			
			$uid = $row->filter('li')->attr('uid');
			
			if($uid){
				
				//存储赞信息
				$like = Wb_like::firstOrNew(['mid' => $this->mid, 'uid'=>$uid]);
				//更新时不必改动项
				if(!$like->exists){
					$like->mid = $this->mid;
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
					$wbUser->usercard = ltrim($href, "\/");
					$wbUser->username = $row->filter('a>img')->attr('title');
					$wbUser->photo_url = $row->filter('a>img')->attr('src');
					$wbUser->save();
				}		

				$page_total++;
			}
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
