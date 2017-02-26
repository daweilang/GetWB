<?php

/**
 *  GetUserComplete.php 抓取用户所有微博数据
 *  使用cn抓取，便于分析
 * @copyright			(C) daweilang
 * @license				https://github.com/daweilang/
 *
 */


namespace App\Libraries\Contracts;

use App\Models\Wb_complete;

use App\Libraries\Classes\WeiboContent;
use Symfony\Component\DomCrawler\Crawler;

use Storage;


class GetUserCompleteCn
{	
	public $usercard;
	protected $userUrl;
	private $file;
	
	public function __construct($usercard)
	{
		$this->usercard = $usercard;
		$this->userUrl = sprintf(config('weibo.WeiboUser.userFace'), $this->usercard);
		$this->file = "wbUserHtml/{$this->usercard}_wb";
	}
	
	
	
	/**
	 * 抓取cn的微博页面
	 */
	public function getUserHtml()
	{	
		$wb = new WeiboContent();
		//抓取
		$content = $wb->getWBHtml($this->userUrl, config('weibo.CookieFileCn.sina'), config('weibo.CookieFileCn.curl'));
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
	 * cn的用户html分析
	 * @param $userHtml html
	 * @param unknown $file 储存的html页面
	 */
	public function explainUserWeibo($userHtml, $file ='')
	{
		if($file && Storage::exists($file)){
			//该页面应该是html
			$userHtml = Storage::get($file);
		}
		$crawler = new Crawler();
		$crawler->addHtmlContent($userHtml);
		
		#### 获取链接中的用户id
		$arrayTmp = $crawler->filterXPath('//div[@class="ut"]')->filter('a')->each(function (Crawler $node, $i) {		
				return [$node->text(),$node->filter('a')->attr('href')];	
		});
		$uid = '';
		foreach($arrayTmp as $k){
			if($k[0] == '资料' && preg_match('/\/(\d+)\/info/' , $k[1], $m)){
				$uid = $m[1];
			}
		}
		if(empty($uid)){
			throw new \Exception("用户页面获取不正确");
		}
		
		#### 第一层span，用户信息
		$arrayTmp = $crawler->filterXPath('//div[@class="ut"]')->filter('span')->each(function (Crawler $node, $i) {
			if($i == 0){
				return ['infos' => $node->text()];
			}
			//介绍
			if($node->attr('style')=='word-break:break-all; width:50px;'){
				return ['intro' => $node->text()];
			}
			elseif ($i == 2){ //认证
				return ['type' => $node->text()];
			}
		});
		$dataArray = [];
		foreach ($arrayTmp as $v => $k){
			if(!empty($k)){
				$dataArray = array_merge($dataArray, $k);
			}
		}
		
		#### 用户粉丝数等信息
		$weibos = $crawler->filterXPath('//div[@class="tip2"]')->filter('span')->text();
		$follow = $crawler->filterXPath('//div[@class="tip2"]')->filterXPath('//a[1]')->text();
		$fans = $crawler->filterXPath('//div[@class="tip2"]')->filterXPath('//a[2]')->text();
		
		$wbUser = Wb_complete::firstOrNew(['uid'=>$uid]);
		//更新时不必改动项
		if(!$wbUser->exists){
			$wbUser->uid = $uid;
			$wbUser->usercard = $this->usercard;
		}
		list($wbUser->username, $male_place) = mb_split('\s', trim($dataArray['infos']));	
		$wbUser->status = 1;
		
		//用户微博关注粉丝数等信息
		if(preg_match('/\[(\d+)\]/' , $weibos, $m)){
			$wbUser->weibos = $m[1];
		}
		if(preg_match('/\[(\d+)\]/' , $follow, $m)){
			$wbUser->follow = $m[1];
		}
		if(preg_match('/\[(\d+)\]/' , $fans, $m)){
			$wbUser->fans = $m[1];
		}
		
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
}
