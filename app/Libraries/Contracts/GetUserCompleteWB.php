<?php

/**
 *  GetUserCompleteWB.php 抓取用户所有微博数据
 *  使用cn抓取，便于分析
 * @copyright		(C) daweilang
 * @license			https://github.com/daweilang/
 *
 */


namespace App\Libraries\Contracts;

use App\Models\Wb_complete;
use App\Models\Wb_user_weibo;

use App\Libraries\Classes\WeiboContent;
use Symfony\Component\DomCrawler\Crawler;

use Storage;


class GetUserCompleteWB
{	
	protected $userUrl;
	private $file;
	private $userinfo;
	
	public function __construct(Wb_complete $userinfo)
	{
		$this->userinfo = $userinfo;
		$this->userUrl = sprintf(config('weibo.WeiboUser.userFace'), $this->userinfo->uid)."?page=%d";
		$this->file = "wbUserHtml/{$this->userinfo->uid}_weibos";
	}
	
	
	
	/**
	 * 抓取cn的微博页面
	 */
	public function getUserWeibos()
	{	
		$wb = new WeiboContent();
		
		Storage::delete($this->file);
		$page_total = 1;
		if($this->userinfo->weibos > 10){
			$page_total = ceil($this->userinfo->weibos/10);
		}
		/**
		 * 考虑到发微博量，需要新建任务处理
		 */
// 		for($page=1;$page<=$page_total;$page++){
		for($page=1;$page<=30;$page++){
			$this->getWeiboHtml($page);
			sleep("1");
		}		
		$this->explainPage();	
	}
	
	
	/**
	 * 抓取评论页面写入文件
	 */
	public function getWeiboHtml($page)
	{
		$wb = new WeiboContent();
		$html = $wb->getWBHtml(sprintf($this->userUrl, $page), config('weibo.CookieFileCn.sina'), config('weibo.CookieFileCn.curl'));
		//抓取
		if(empty($html)){
			throw new \Exception("无法获取微博列表，请检查");
		}
		$crawler = new Crawler();
		$crawler->addHtmlContent($html);
		$div = $crawler->filter('body')->html();
		
		Storage::append($this->file, $div);
		if(!Storage::exists($this->file)){
			throw new \Exception("无法储存微博评论页面");
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
		if(empty($html) && Storage::exists($this->file)){
			//该页面应该是html
			$html = Storage::get($this->file);
		}
		else{
			throw new \Exception("微博列表为空，请检查");
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
					'uid'=> $this->userinfo->uid,
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
