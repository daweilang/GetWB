<?php

/**
 *  GetWeiboInfo.php 抓取微博信息封装
 *
 * @copyright			(C) daweilang
 * @license				https://github.com/daweilang/
 *
 */


namespace App\Libraries\Contracts;

use App\Models\Weibo;
use App\Libraries\Classes\WeiboContent;
use Symfony\Component\DomCrawler\Crawler;

use Config;
use Storage;


class GetWeiboInfo
{
	private $cookieWeibo;
	private $cookieCurl;
	
	//加载配置
	private $config;
	
	public  $mid;
	
	protected $weibo;
	private $wbFile;
	
	public function __construct(Weibo $weibo)
	{
		
		$this->weibo = $weibo;
		
		//加载微博登录配置
		$this->config = Config::get('weibo');
		//微博cookie
		$this->cookieWeibo = $this->config['CookieFile']['weibo'];
		$this->cookieCurl =  $this->config['CookieFile']['curl'];
		
		//微博储存地址
		$this->wbFile = "wbHtml/weibo_{$this->weibo->id}_page";
	}
	
	
	/**
	 * 抓取微博信息写入文件
	 * @param unknown $url
	 * @param unknown $file
	 * @return boolean
	 */
	public function getWeiboHtml()
	{
		$wb = new WeiboContent();
		//测试抓取
		$content = $wb->getWBHtml($this->weibo->wb_url, $this->cookieWeibo, $this->cookieCurl);		
		$isLogin = $wb->requiresLogin($content);
		
		if($isLogin){
			throw new \Exception("微博登录失效，请重新授权");
		}
		
		Storage::put($this->wbFile, $content);
		if(!Storage::exists($this->wbFile)){
			throw new \Exception("无法储存微博页面");
		}
		return $content;
	}
	
	/**
	 * 根据微博内容抓取第一页评论和赞分析
	 * @param unknown $wbHtml
	 * @param string $file $this->wbFile
	 */
	public function explainWeibo($html, $file ='')
	{
		if($file && Storage::exists($file)){
			$html = Storage::get($file);
		}
		
		//获得用户基本信息
		if(empty($uid = $this->usePregMatch(config('weibo.WeiboInfo.oid'), $html))){
			throw new \Exception("用户页面获取不正确");
			return false;
		}

		$crawler = new Crawler();
		$crawler->addHtmlContent($html);
		$title = $crawler->filter('title')->text();
		$crawler->clear();
		
		//获得微博综合信息部分
		$data = json_decode($this->usePregMatch(config('weibo.WeiboInfo.weiboInfoView'), $html), true);	
		$crawler->addHtmlContent($data['html']);
		
		#### 获取链接中的用户id
		if(empty($this->mid = $crawler->filterXPath('//div[contains(@node-type, "root_child_comment_build")]')->attr('mid'))){
			throw new \Exception("微博页面获取不正确");
			return false;
		}	
		
		$time = $crawler->filterXPath('//div[@class="WB_from S_txt2"]')->filter('a')->first()->attr('date');
		$wb_created = date("Y-m-d H:i:s", substr($time, 0, 10));
		
		$weiboInfo = [];
		$crawler->filterXPath('//div[@class="WB_handle"]/ul/li')->each(function (Crawler $row, $i) use (&$weiboInfo){
			$weiboInfo[$row->filter('a')->attr('action-type')] = $row->filter('a')->filter('em')->last()->text();;
		});	

		$this->weibo->title = $title;
		$this->weibo->mid = $this->mid;
		$this->weibo->uid = $uid;
		$this->weibo->comment_total = $weiboInfo['fl_comment'];
		$this->weibo->like_total = $weiboInfo['fl_like'];
		$this->weibo->forward_total = $weiboInfo['fl_forward'];
		$this->weibo->wb_created = $wb_created;
		$this->weibo->status = 1;
		$this->weibo->save();
				
		return true;
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
