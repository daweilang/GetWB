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
	 * 判断微博cookie是否可用
	 */
	public function weiboCookieIsUse()
	{
		return true;
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
	public function explainWeibo($wbHtml, $file ='')
	{
		if($file && Storage::exists($file)){
			$wbHtml = Storage::get($file);
		}
		
		###该页使用js输出，内容不能使用crawler分析
		$crawler = new Crawler();
		$crawler->addHtmlContent($wbHtml);
		//返回新浪通行证
		$title = $crawler->filter('title')->text();
		
		if(preg_match($this->config['WeiboInfo']['pregCommentId'], $wbHtml , $match)){
			
			$this->mid = $match['1'];

			$wb = new WeiboContent();
			
			//第一页评论地址，获得评论页内容
			$comment = sprintf($this->config['WeiboInfo']['commentUrl'], $this->mid, 1);
			$content = $wb->getWBHtml($comment, $this->cookieWeibo, $this->cookieCurl);			
			$data = json_decode($content, true);
			$pageCommnetData = $this->getWeiboCommnetInfo($data);
			$commentFile = "wbHtml/weibo_". $this->weibo->id ."_commnet";
			Storage::put($commentFile, $content, true);
			
			//获得赞页内容，获得赞页列表
			$like = sprintf($this->config['WeiboInfo']['likeUrl'], $this->mid, 1);
			$content = $wb->getWBHtml($like, $this->cookieWeibo, $this->cookieCurl);			
			$data = json_decode($content, true);
			$pageLikeData = $this->getWeiboLikeInfo($data);
			$likeFile = "wbHtml/weibo_". $this->weibo->id ."_like";
			Storage::put($likeFile, $content, true);
			
			if(preg_match($this->config['WeiboInfo']['oid'], $wbHtml , $m)){
				$this->weibo->wb_userid = $m[1];
			}
			
			$this->weibo->wb_title = $title;
			$this->weibo->wb_mid = $this->mid;
			$this->weibo->wb_comment_page = $pageCommnetData['totalpage'];
			$this->weibo->wb_comment_total = $pageCommnetData['count'];
			$this->weibo->wb_like_page = $pageLikeData['totalpage'];
			$this->weibo->wb_like_total = $pageLikeData['count'];
			$this->weibo->wb_status = 1;
			$this->weibo->save();
				
			return true;
		}
		else{
			Log::info("无法获得微博页面");
			throw new \Exception("无法获得微博页面");
			return false;
		}
		
	}
	
	
	/**
	 * 根据微博内容抓取第一页评论分析
	 * @param unknown $file
	 * @param $commentFile 存储的评论页
	 */
	public function explainWeiboComment()
	{
		$file = "wbHtml/weibo_". $this->weibo->id ."_page";
		if(!Storage::exists($file)){
			return false;
		}
		
		$content = Storage::get($file);
		
		//获得微博title
		preg_match($this->config['WeiboInfo']['pregTitle'], $content , $m);
				
		if(preg_match($this->config['WeiboInfo']['pregCommentId'], $content , $match)){
			
			$this->mid = $match['1'];
			//第一页评论地址
			$comment = sprintf($this->config['WeiboInfo']['commentUrl'], $this->mid, 1);
			$wb = new WeiboContent();
			//获得评论页内容
			$content = $wb->getWBHtml($comment, $this->cookieWeibo, $this->cookieCurl);
			
			$data = json_decode($content, true);
			$pageData = $this->getWeiboCommnetInfo($data);

			$this->weibo->wb_title = $m['1'];
			$this->weibo->wb_mid = $this->mid;
			$this->weibo->wb_comment_page = $pageData['totalpage'];
			$this->weibo->wb_comment_total = $pageData['count'];
			$this->weibo->wb_status = 1;
			$this->weibo->save();
			
			$commentFile = "wbHtml/weibo_". $this->weibo->id ."_commnet";
			Storage::put($commentFile, $content, true);
			if(!Storage::exists($file)){
				throw new \Exception("无法储存微博页面");
			}
			return true;
		}
		else{
			throw new \Exception("无法获得微博评论页面");
			return false;	
		}
	}
	
	
	/**
	 * 
	 * 根据评论页面信息获得weibo的评论明细
	 * @param array $data 评论数组
	 * @param unknown $file, 使用storage储存的页面
	 * 两种方式，评论保存临时文件或者评论数组
	 * @throws \Exception
	 * @return unknown[]|mixed[] 返回评论总页数，总评论数
	 */
	public function getWeiboCommnetInfo( $commentData, $file =''){
		
		if(Storage::exists($file)){
			//该页面应该是直接抓取json数据
			$commentData = json_decode(Storage::get($file),true);
		}
		if($commentData['code'] != '100000'){
			//获取评论错误
			throw new \Exception($commentData['msg']);
		}
		return [
				'totalpage' => $commentData['data']['page']['totalpage'], 
				'count' => $commentData['data']['count']
				];
	}	

	/**
	 * 
	 * 根据评论页面信息获得weibo的评论明细
	 * @param array $data 点赞用户数组
	 * @param unknown $file, 使用storage储存的页面
	 * 两种方式，评论保存临时文件或者评论数组
	 * @throws \Exception
	 * @return unknown[]|mixed[] 返回评论总页数，总评论数
	 */
	public function getWeiboLikeInfo( $likeData, $file =''){
		
		if(Storage::exists($file)){
			//该页面应该是直接抓取json数据
			$likeData = json_decode(Storage::get($file),true);
		}
		if($likeData['code'] != '100000'){
			//获取评论错误
			throw new \Exception($likeData['msg']);
		}
		return [
				'totalpage' => $likeData['data']['page']['totalpage'], 
				'count' => $likeData['data']['total_number']
				];
	}	
}
