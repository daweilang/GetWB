<?php

/**
 *  CompleteWeiboInfo.php 抓取微博信息封装
 *  
 *	该程序只完善微博信息，对评论、赞、和转发数据不做具体分析
 *
 *	微博需要的信息包括，mid、code和uid用户id
 *  微博地址规则 http://weibo.com/uid/code
 * 
 * @copyright		(C) daweilang
 * @license			https://github.com/daweilang/
 *
 */


namespace App\Libraries\Contracts;

use App\Libraries\Classes\WeiboContent;
use Symfony\Component\DomCrawler\Crawler;

use App\Models\Wb_user_weibo;

use Config;
use Storage;



class CompleteWeiboInfo
{
	/**
	 * 程序需要使用的cookie文件
	 * @var string file
	 */
	private $cookieWeibo;
	private $cookieCurl;
	
	/**
	 * 储存配置
	 * @var array
	 */
	private $config;
	
	/**
	 * 储存的文件相关设置
	 * @var unknown
	 */
	private $filePath;
	private $file;
	
	
	protected $weibo;
	
	
	public function __construct(Wb_user_weibo $weibo)
	{
		
		$this->weibo = $weibo;
		
		//加载微博登录配置
		$this->config = Config::get('weibo');
		
		//微博cookie
		$this->cookieWeibo = $this->config['CookieFile']['weibo'];
		$this->cookieCurl =  $this->config['CookieFile']['curl'];
		
		//微博储存文件地址
		$this->filePath = "wbCompleteHtml/{$this->weibo->uid}";
		$this->file = "{$this->filePath}/weibo_{$this->weibo->code}_page";
	}
	
	
	/**
	 * 判断微博cookie是否可用
	 * 该判断需要对微博进行二次抓取，所以暂不使用
	 */
	public function weiboCookieIsUse()
	{
		//测试抓取
		$wb_url = $this->getWeiboUrl();
		$content = $wb->getWBHtml($wb_url, $this->cookieWeibo, $this->cookieCurl);
		$isLogin = $wb->requiresLogin($content);
		
		if($isLogin){
			throw new \Exception("微博登录失效，请重新授权");
		}
		return true;
	}
	
	
	/**
	 * 根据传输的参数获得微博url
	 */
	private function getWeiboUrl()
	{
		if($this->weibo->code && $this->weibo->uid){
			return sprintf("http://weibo.com/%s/%s", $this->weibo->uid, $this->weibo->code);
		}
		else{
			return false;	
		}
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
		$wb_url = $this->getWeiboUrl();
		
		if($wb_url === false){
			throw new \Exception("微博地址错误");
		}
		
		$content = $wb->getWBHtml($wb_url, $this->cookieWeibo, $this->cookieCurl);		
		$isLogin = $wb->requiresLogin($content);
		
		if($isLogin){
			throw new \Exception("微博登录失效，请重新授权");
		}
		
		Storage::put($this->file, $content);
		if(!Storage::exists($this->file)){
			throw new \Exception("无法储存微博页面");
		}
		return $content;
	}
	
	/**
	 * 根据微博内容获得微博信息
	 * @param unknown $wbHtml
	 * @param string $file $this->file
	 */
	public function explainWeibo($html = '')
	{	
		if(empty($html) && Storage::exists($this->file)){
			//该页面应该是html
			$html = Storage::get($this->file);
		}
		elseif(empty($html)){
			throw new \Exception("微博列表为空，请检查");
		}
		
		###该页使用js输出，内容不能使用crawler分析
		$crawler = new Crawler();
		$crawler->addHtmlContent($html);
		//返回新浪通行证
		$title = $crawler->filter('title')->text();
		
		if(preg_match($this->config['WeiboInfo']['pregCommentId'], $html , $m)){
			
			$mid = $m['1'];

			$wb = new WeiboContent();
			
			//第一页评论地址，获得评论页内容
			$comment = sprintf($this->config['WeiboInfo']['commentUrl'], $mid, 1);
			$content = $wb->getWBHtml($comment, $this->cookieWeibo, $this->cookieCurl);
			$data = json_decode($content, true);
			$pageCommnetData = $this->getWeiboCommnetInfo($data);
			$commentFile = "{$this->filePath}/weibo_". $this->weibo->code ."_commnet";
			Storage::put($commentFile, $content, true);
			
			
			//获得赞页内容，获得赞页列表
			$like = sprintf($this->config['WeiboInfo']['likeUrl'], $mid, 1);
			$content = $wb->getWBHtml($like, $this->cookieWeibo, $this->cookieCurl);
			$data = json_decode($content, true);
			$pageLikeData = $this->getWeiboLikeInfo($data);
			$likeFile = "{$this->filePath}/weibo_". $this->weibo->code ."_like";
			Storage::put($likeFile, $content, true);
			
			
			
			$this->weibo->title = $title;
			$this->weibo->mid = $mid;
			$this->weibo->comment_total = $pageCommnetData['count'];
			$this->weibo->comment_page = $pageCommnetData['totalpage'];
			$this->weibo->like_total = $pageLikeData['total_number'];
			$this->weibo->like_page = $pageLikeData['totalpage'];
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
	 *
	 * 根据评论页面信息获得weibo的评论明细
	 * @param array $data 点赞用户数组
	 * @param unknown $file, 使用storage储存的页面
	 * 两种方式，评论保存临时文件或者评论数组
	 * @throws \Exception
	 * @return unknown[]|mixed[] 返回评论总页数，总评论数
	 */
	private function getWeiboLikeInfo( $likeData, $file =''){
	
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
				'total_number' => $likeData['data']['total_number']
		];
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
	 * 业务逻辑
	 */
	public function Process()
	{
		/**
		 * 使用返回的html，省略读取文件
		 * @var Ambiguous $html
		 */
		$html = $this->getWeiboHtml();
		$this->explainWeibo($html);
	}
}
