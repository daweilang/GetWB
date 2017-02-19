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
		$this->mid = $mid;	
		
		/**
		 * 没有指定使用模型时，默认使用weibos表数据
		 * @var model name
		 */
		if($model){
			$this->model = $model;
		}
	}
	
	
	/**
	 * 根据赞页数，设置评论页队列任务
	 */
	public function setJob($jobName='')
	{
		$model = "\App\Models\\$this->model";
		$weibo = $model::where('mid', $this->mid)->first();
		
		for($page=1;$page<=$weibo->like_page;$page++){
			
			/**
			 * like任务表登记任务
			 * @var \App\Models\Wb_like_job $like_job
			 */
			$like_job = Wb_like_job::create( [ 'mid' => $this->mid, 'j_like_page' => $page, 'model'=>$this->model]);
			
			/**
			 * 设置任务 $like_job 数据表储存了需要抓取数据的来源
			 */
			if(empty($jobName)){
				$job = (new GetLikeContentJob($like_job))->delay($this->delay);
			}
			else{
				$job = (new GetLikeContentJob($like_job))->onQueue($jobName)->delay($this->delay);
			}
			dispatch($job);
		}
	}
	
	/**
	 * 抓取评论页面写入文件
	 */
	public function getHtml($page)
	{
		/**
		 * 赞接口地址
		 */
		$this->thisUrl = sprintf(config('weibo.WeiboInfo.likeUrl'), $this->mid, $page);
		
		$file = "wbHtml/$this->mid/like_$page";
		$wb = new WeiboContent();
		//抓取
		$content = $wb->getWBHtml($this->thisUrl, config('weibo.CookieFile.weibo'), config('weibo.CookieFile.curl'));
		
		$array = json_decode($content, true);
		if(!is_array($array) || $array['code'] !== '100000'){
			Storage::put("wbHtml/$this->mid/error_$page", $content);
			throw new \Exception("无法获取接口，请检查获取结果");
		}
		$html = $array['data']['html'];
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
		
		$model = "\App\Models\\$this->model";
		$weibo = $model::where('mid', $this->mid)->first();
		$oid = $weibo->uid;
		
		$crawler->filterXPath('//div[@class="WB_emotion"]')->filter('li')->each(function (Crawler $row) use ($oid) {
			
			$uid = $row->filter('li')->attr('uid');
			
			if($uid){
				
				//储存用户信息
				$wbUser = Wb_user::firstOrNew(['uid'=>$uid]);
				//后台执行抓取用户信息程序
				if(!$wbUser->exists){
					$wbUser->uid = $uid;
					$href = $row->filter('a')->attr('href');
					if(preg_match('/\/(\w+)$/', $href , $m)){
						$wbUser->usercard = $m[1];
					}
					$wbUser->username = $row->filter('a>img')->attr('title');
					$wbUser->photo_url = $row->filter('a>img')->attr('src');
					$wbUser->save();
				}
			
				$like = Wb_like::firstOrNew(['mid' => $this->mid, 'uid'=>$uid]);
				//更新时不必改动项
				if(!$like->exists){
					$like->mid = $this->mid;
					$like->uid = $uid;
					$like->oid = $oid;
					$like->save();
				}				
			}
		});
	}

}
