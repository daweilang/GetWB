<?php

/**
 *  GetComment.php 抓取评论相关
 *  该程序为旧接口，评论和评论回复一起输出，没有父子关系
 * @copyright			(C) daweilang
 * @license				https://github.com/daweilang/
 *
 */


namespace App\Libraries\Contracts;

use App\Models\Weibo;
use App\Models\Wb_like;
use App\Models\Wb_like_job;
use App\Models\Wb_user;

use App\Jobs\GetLikeContentJob;
use App\Libraries\Classes\WeiboContent;
use Symfony\Component\DomCrawler\Crawler;

use Storage;


class GetLike
{	
	//该条微博id
	public $mid;
	//微博所属用户id
	public $oid;
	
	
	public function __construct($mid)
	{
		$this->mid = $mid;
	}
	
	
	/**
	 * 判断微博cookie是否可用
	 */
	public function weiboCookieIsUse()
	{
		return true;
	}
	
	
	/**
	 * 根据赞页数，设置评论页队列任务
	 */
	public function setLikeJob()
	{
		$weibo = Weibo::where('wb_mid', $this->mid)->first();
		for($page=1;$page<=$weibo->wb_like_page;$page++){
// 		for($page=1;$page<=3;$page++){
			//插入表数据
			$like_job = Wb_like_job::create( [ 'mid' => $this->mid, 'j_like_page' => $page, ]);
			//设置job
			$job = (new GetLikeContentJob($like_job))->delay(10);
			//多进程时候使用命名
// 			$job = (new GetLikeContentJob($like_job))->onQueue('GetLike')->delay(10);
			dispatch($job);
		}
	}
	
	/**
	 * 抓取评论页面写入文件
	 */
	public function getLikeHtml($page)
	{
		//赞接口地址
		$likeUrl = sprintf(config('weibo.WeiboInfo.likeUrl'), $this->mid, $page);
		$file = "wbHtml/$this->mid/like_$page";
		$wb = new WeiboContent();
		//抓取
		$content = $wb->getWBHtml($likeUrl, config('weibo.CookieFile.weibo'), config('weibo.CookieFile.curl'));
		
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
	public function explainLikePage($html, $file ='')
	{
		if($file && Storage::exists($file)){
			//该页面应该是html
			$html = Storage::get($file);
		}
		
		$crawler = new Crawler();
		$crawler->addHtmlContent($html);
		
		$weibo = Weibo::where('wb_mid', $this->mid)->first();
		$oid = $weibo->wb_userid;
		
		$crawler->filterXPath('//div[@class="WB_emotion"]')->filter('li')->each(function (Crawler $row) use ($oid) {
			
			$uid = $row->filter('li')->attr('uid');
			
			if($uid){
				
				//储存用户信息
				$wbUser = Wb_user::firstOrNew(['uid'=>$uid]);
				//更新时不必改动项
				if(!$wbUser->exists){
					$wbUser->uid = $uid;
				}
				$href = $row->filter('a')->attr('href');
				if(preg_match('/\/(\w+)$/', $href , $m)){
					$wbUser->usercard = $m[1];
				}
				$wbUser->username = $row->filter('a>img')->attr('title');
				$wbUser->photo_url = $row->filter('a>img')->attr('src');
				$wbUser->save();
			
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
