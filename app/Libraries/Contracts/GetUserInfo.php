<?php

/**
 *  GetComment.php 抓取评论相关
 *  该程序为旧接口，评论和评论回复一起输出，没有父子关系
 * @copyright			(C) daweilang
 * @license				https://github.com/daweilang/
 *
 */


namespace App\Libraries\Contracts;

use App\Models\Wb_user;

use App\Libraries\Classes\WeiboContent;
use Symfony\Component\DomCrawler\Crawler;

use Storage;


class GetUserInfo
{	
	public $usercard;
	protected $userUrl;
	private $file;
	
	public function __construct($usercard)
	{
		$this->usercard = $usercard;
		$this->userUrl = sprintf(config('weibo.WeiboUser.userFace'), $this->usercard);
		$this->file = "wbUserHtml/{$this->usercard}_face";
	}
	
	
	
	/**
	 * 抓取评论页面写入文件
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
	 * 获得评论的html分析
	 * @param $commentHtml 评论的html
	 * @param unknown $file 评论储存的html页面
	 */
	public function explainUserFace($userHtml, $file ='')
	{
		if($file && Storage::exists($this->file)){
			//该页面应该是html
			$userHtml = Storage::get($this->file);
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
		
		$wbUser = Wb_user::firstOrNew(['uid'=>$uid]);
		//更新时不必改动项
		if(!$wbUser->exists){
			$wbUser->uid = $uid;
			$wbUser->usercard = $this->usercard;
		}
		list($wbUser->username, $male_place) = mb_split('\s', trim($dataArray['infos']));
		list($wbUser->male, $wbUser->place) = explode("/", $male_place);
		//类型，认证类型
		if(isset($dataArray['type'])){
			$wbUser->type = $dataArray['type'];
		}
		if(isset($dataArray['intro'])){
			$wbUser->intro = $dataArray['intro'];
		}	
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
		return $this->explainUserFace($html);
	}
}
