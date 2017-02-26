<?php

/**
 *  GetCompleteWB.php 抓取用户所有微博数据
 *  使用cn抓取，便于分析
 * @copyright		(C) daweilang
 * @license			https://github.com/daweilang/
 *
 */


namespace App\Libraries\Contracts;

use App\Models\Wb_complete;
use App\Models\Wb_complete_job;
use App\Models\Wb_user_weibo;

use App\Libraries\Classes\WeiboContent;
use App\Libraries\Classes\TraitGetConfig;

use App\Jobs\GetCompleteWBJob;

use Symfony\Component\DomCrawler\Crawler;

use Storage;


class GetCompleteWBCn
{	
	use TraitGetConfig;
	
	private $userUrl;
	private $file;
	private $userinfo;
	private $uid;
	
	public function __construct($uid)
	{
		$this->uid = $uid;
		
		$this->getQueueConf();
	}
	
	
	/**
	 * 根据微博数，设置抓取微博任务
	 */
	public function setJob($jobName='')
	{
		$this->userinfo = Wb_complete::where('uid', $this->uid)->first();
		
		$page_total = 1;
		if($this->userinfo->weibos > 10){
			$page_total = ceil($this->userinfo->weibos/10);
		}		
		for($page=1;$page<=$page_total;$page++){
				
			/**
			 * complete任务表登记任务
			 * @var \App\Models\Wb_complete_job $complete_job
			 */
			$complete_job = Wb_complete_job::create( [ 'uid' => $this->uid, 'j_complete_page' => $page,]);
				
			/**
			 * 设置任务 $like_job 数据表储存了需要抓取数据的来源
			 */
			if(empty($jobName)){
				$job = (new GetCompleteWBJob($complete_job))->delay($this->delay);
			}
			else{
				$job = (new GetCompleteWBJob($complete_job))->onQueue($jobName)->delay($this->delay);
			}
			dispatch($job);
		}
		
		/**
		 * 设置完微博抓取任务
		 */
		$this->userinfo->status = 2;
		$this->userinfo->save();
	}
	
	
	/**
	 * 抓取评论页面写入文件
	 */
	public function getHtml($page)
	{
		/**
		 * 使用cn微博页面抓取
		 * @var \App\Libraries\Contracts\GetCompleteWB $userUrl
		 */
		$this->userUrl = sprintf(config('weibo.WeiboUser.userFace'), $this->uid)."?page=%d";
		$this->file = "wbUserHtml/{$this->uid}_weibos_$page";
		
		$wb = new WeiboContent();
		$html = $wb->getWBHtml(sprintf($this->userUrl, $page), config('weibo.CookieFileCn.sina'), config('weibo.CookieFileCn.curl'));
		
		/**
		 * 抓取cn列表页
		 */
		if(empty($html)){
			throw new \Exception("无法获取微博列表，请检查");
		}
		$crawler = new Crawler();
		$crawler->addHtmlContent($html);
		$div = $crawler->filter('body')->html();
		
		Storage::put($this->file, $div);
		if(!Storage::exists($this->file)){
			throw new \Exception("无法储存微博列表页面");
		}
		return $div;
	}
	
	
	/**
	 * 获得评论的html分析
	 * @param $commentHtml 评论的html
	 * @param unknown $file 评论储存的html页面
	 */
	public function explainPage($html='')
	{
		if(empty($html)){
			if(Storage::exists($this->file)){
				//该页面应该是html
				$html = Storage::get($this->file);
			}
			else{
				throw new \Exception("微博列表为空，请检查");
			}
		}
		
		$crawler = new Crawler();
		$crawler->addHtmlContent($html);
		$crawler->filterXPath('//div[@class="c"]')->each(function (Crawler $row) {
			//获得weibo码
			$mcode = $row->filter('div')->attr('id');
			if(empty($mcode)) return false;
			$code = explode("_", $mcode)[1];
			$text = $row->filter('div')->last()->text();
			//赞[0] 转发[0] 评论[0]
			if(preg_match_all('/赞\[(\d+)\]|转发\[(\d+)\]|评论\[(\d+)\]/', $text, $m)){
				$like = $m[1][0];
				$repost = $m[2][1];
				$comment = $m[3][2];
			}
			
			$timeText = $row->filterXPath('//span[@class="ct"]')->html();
			$created = '';
			
			if(preg_match('/(\d{4}\-\d{2}\-\d{2}\s+\d{2}\:\d{2}:\d{2})/', $timeText, $m)){
				$created = $m[1]; 
			}
			elseif(preg_match('/(\d{1,2})月(\d{1,2})日\s+(\d{1,2})\:(\d{1,2})/', $timeText, $m)){
				//01月16日 13:20
				$created = sprintf("%04d-%02d-%02d %02d:%02d:00", date("Y"), $m[1], $m[2], $m[3], $m[4]);
			}
			elseif(preg_match('/今天\s+(\d{1,2})\:(\d{1,2})/', $timeText, $m)){
				//01月16日 13:20
				$created = sprintf("%04d-%02d-%02d %02d:%02d:00", date("Y"), date("m"), date("d"), $m[1], $m[2]);
			}
			$wbInfo = [
					'uid'=> $this->uid,
					'code'=> $code,
					'comment_total' => $comment,
					'like_total' => $like,
					'repost_total' => $repost,
					'wb_created' => $created,
			];
			$this->saveWbInfo($wbInfo);
				
		});
	}
	
	
	/**
	 * 封装数据插入
	 * @param unknown $wbInfo
	 */
	private function saveWbInfo($wbInfo)
	{
		$wb = Wb_user_weibo::firstOrNew(['code' => $wbInfo['code']]);
		//更新时不必改动项
		if(!$wb->exists){
			$wb->code = $wbInfo['code'];
		}
		$wb->uid = $wbInfo['uid'];
		$wb->comment_total = $wbInfo['comment_total'];
		$wb->like_total = $wbInfo['like_total'];
		$wb->repost_total = $wbInfo['repost_total'];
		$wb->wb_created = $wbInfo['wb_created'];
		$wb->save();
	}
	
}
