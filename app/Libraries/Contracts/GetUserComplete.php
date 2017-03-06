<?php

/**
 *  GetUserComplete.php 获得用户基本信息
 *  由于cn页面信息不完善，改为用com页面抓取
 *  http://weibo.com/%s中含有domain和page_id，可以用来获得用户微博接口
 *  
 *  2017-02-24
 * @copyright			(C) daweilang
 * @license				https://github.com/daweilang/
 *
 */


namespace App\Libraries\Contracts;

use App\Models\Wb_complete;

use App\Libraries\Classes\WeiboContent;
use Symfony\Component\DomCrawler\Crawler;

use Storage;


class GetUserComplete
{	
	public $usercard;

	/**
	 * 该逻辑只分析一个地址，所以将url和file预定义
	 * @var unknown
	 */
	private $userUrl;
	private $file;
	
	public function __construct($usercard)
	{
		$this->usercard = $usercard;
		//获得用户首页
		$this->userUrl = sprintf(config('weibo.WeiboUser.userWebFace'), $this->usercard);
		$this->file = "wbUserHtml/{$this->usercard}_web_face";
	}
	
	
	
	/**
	 * 抓取com的微博页面
	 */
	public function getUserHtml()
	{	
		$wb = new WeiboContent();
		//抓取
		$content = $wb->getWBHtml($this->userUrl, config('weibo.CookieFile.weibo'), config('weibo.CookieFile.curl'));
		if(!$content){
			throw new \Exception("无法获取微博用户信息！ url:". $this->userUrl);
		}
		Storage::put($this->file, $content);
		if(!Storage::exists($this->file)){
			throw new \Exception("无法储存微博用户信息！");
		}
		return $content;
	}
	
	
	/**
	 * 用户首页html分析
	 * @param $userHtml html
	 * @param unknown $file 储存的html页面
	 * 用的com首页为展示页面，比较特殊，只能使用正则匹配来分析
	 */
	public function explainUserWeibo($html = '')
	{
		if(empty($html) && Storage::exists($this->file)){
			//该页面应该是html
			$html = Storage::get($this->file);
		}
		
		//获得用户基本信息
		if(empty($uid = $this->usePregMatch(config('weibo.WeiboInfo.oid'), $html))){
			throw new \Exception("用户页面获取不正确");
		}		
		$domain = $this->usePregMatch(config('weibo.WeiboInfo.domain'), $html);
		$page_id = $this->usePregMatch(config('weibo.WeiboInfo.page_id'), $html);
		$username = $this->usePregMatch(config('weibo.WeiboInfo.onick'), $html);
		
		//获得粉丝关注微博数
		$userInfo = [];
		$data = json_decode($this->usePregMatch(config('weibo.WeiboInfo.userInfoView'), $html), true);
		if($data['html']){
			$crawler = new Crawler();
			$crawler->addHtmlContent($data['html']);
			
			#### 获取链接中的用户id
			$crawler->filterXPath('//td[@class="S_line1"]')->each(function (Crawler $node, $i) use (&$userInfo){
				$userInfo[$node->filter('span')->text()] = $node->filter('strong')->text();
			});
		}
		$wbUser = Wb_complete::firstOrNew(['uid'=>$uid]);
		//更新时不必改动项
		if(!$wbUser->exists){
			$wbUser->uid = $uid;
			$wbUser->usercard = $this->usercard;
		}
		$wbUser->username = $username;
		$wbUser->domain = $domain;
		$wbUser->page_id = $page_id;
		
		//用户微博关注粉丝数等信息
		$wbUser->weibos = $userInfo['微博'];
		$wbUser->fans = $userInfo['粉丝'];
		$wbUser->follow = $userInfo['关注'];
		
		$wbUser->status = 1;
		
		if($wbUser->save()){
			return $wbUser;
		}
		else{
			return false;
		}
	}
	
	
	/**
	 * 返回用户新的信息
	 * @return boolean|unknown
	 */
	public function GetUserInfoProcess()
	{
		//获得微博用户页面内容
		$html = $this->getUserHtml();
		//分析微博用户信息并储存数据
		$this->explainUserWeibo($html);
		return '';
	}

	/**
	 * 
	 * @param unknown $pattern
	 * @param unknown $subject
	 * @return unknown|boolean
	 */
	private function usePregMatch($pattern, $subject){
		if(preg_match($pattern, $subject , $m)){
			return $m[1];
		}else{
			return false;
		}
	}
}
