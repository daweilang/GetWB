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
	 * 根据评论页数，设置评论页队列任务
	 */
	public function setJob($jobName='')
	{
		$model = "\App\Models\\$this->model";
		$weibo = $model::where('mid', $this->mid)->first();
		for($page=1;$page<=$weibo->comment_page;$page++){
			//插入表数据
			$comment_job = Wb_comment_job::create( [ 'mid' => $this->mid, 'j_comment_page' => $page, ]);
			//设置job
			if(empty($jobName)){
				$job = (new GetCommentContentJob($comment_job))->delay($this->delay);
			}
			else{
				//多进程时候使用命名
				$job = (new GetCommentContentJob($comment_job))->onQueue($jobName)->delay($this->delay);
			}
			dispatch($job);
		}
	}
	
	/**
	 * 抓取评论页面写入文件
	 */
	public function getHtml($page)
	{
		//评论页地址
		$this->thisUrl = sprintf(config('weibo.WeiboInfo.commentUrl'), $this->mid, $page);
		$file = "wbHtml/$this->mid/comment_$page";
		$wb = new WeiboContent();
		//抓取
		$content = $wb->getWBHtml($this->thisUrl, config('weibo.CookieFile.weibo'), config('weibo.CookieFile.curl'));
		
		$array = json_decode($content, true);
		if(!is_array($array) || $array['code'] !== '100000'){
			Storage::put("wbHtml/$this->mid/error_$page", $content);
			throw new \Exception("无法获取微博评论，请检查获取结果");
		}
		$html = $array['data']['html'];
		Storage::put($file, $html);
		if(!Storage::exists($file)){
			throw new \Exception("无法储存微博评论页面");
		}
		return $html;
	}
	
	
	/**
	 * 获得评论的html分析
	 * @param $commentHtml 评论的html
	 * @param unknown $file 评论储存的html页面
	 */
	public function explainPage($commentHtml, $file ='')
	{
		if($file && Storage::exists($file)){
			//该页面应该是html
			$commentHtml = Storage::get($file);
		}
		$crawler = new Crawler();
		$crawler->addHtmlContent($commentHtml);
		
		$crawler->filterXPath('//div[@class="list_li S_line1 clearfix"]')->each(function (Crawler $row) {		
			$wbCommentId = $row->filterXPath('//div[@class="list_li S_line1 clearfix"]')->filter('div')->attr('comment_id');
			$wbComment = Wb_comment::firstOrNew(['comment_id'=>$wbCommentId]);
			//更新时不必改动项
			if(!$wbComment->exists){	
				$wbComment->mid = $this->mid;
				$wbComment->comment_id = $wbCommentId;
			}
			$wbComment->wb_face = $row->filterXPath('//div[@class="WB_face W_fl"]')->filter('a')->attr('href');
			$usercard = $row->filterXPath('//div[@class="WB_face W_fl"]')->filter('a>img')->attr('usercard');
			$uid = '';
			if(preg_match('/id\=(\d+)/', $usercard , $match)){			
				$wbComment->wb_usercard = $uid = $match[1];
			}
			$wbComment->wb_username = $username = $row->filterXPath('//div[@class="WB_text"]')->filter('a')->text();
			$text = trim($row->filterXPath('//div[@class="WB_text"]')->text());
			$wbComment->wb_content = mb_substr($text,mb_strlen($wbComment->wb_username."：",'UTF-8'), null, 'UTF-8');
			if($row->filterXPath('//div[@class="WB_media_wrap clearfix"]')->getNode(0)){
				$wbComment->wb_comment_pic_url = $row->filterXPath('//div[@class="media_box"]')->filter('ul>li>img')->attr('src');
			}
			$wbComment->save();
				
			if($uid){	
				//储存用户信息
				$wbUser = Wb_user::firstOrNew(['uid'=>$uid]);
				//后台执行抓取用户信息程序
				if(!$wbUser->exists){
					$wbUser->uid = $uid;
					$wbUser->username = $username;
					$wbUser->save();
				}	
			}
			
		});
	}
}
