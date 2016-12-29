<?php

/**
 *  GetWeiboInfo.php 抓取微博信息封装
 *
 * @copyright			(C) daweilang
 * @license				https://github.com/daweilang/
 *
 */


namespace App\Libraries\Contracts;

use Log;
use App\Libraries\Classes\WeiboContent;
use App\Models\Weibo;
use Config;
use Storage;


class GetWeiboInfo
{
	private $cookieWeibo;
	private $cookieCurl;
	
	//加载配置
	private $config;
	public  $gid;
	
	protected $weibo;
	
	public function __construct(Weibo $weibo)
	{
		
		$this->weibo = $weibo;
		
		//加载微博登录配置
		$this->config = Config::get('weibo');
		//微博cookie
		$this->cookieWeibo = $this->config['CookieFile']['weibo'];
		$this->cookieCurl =  $this->config['CookieFile']['curl'];
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
		$file = "wbHtml/weibo_". $this->weibo->id ."_page";
		$wb = new WeiboContent();
		//测试抓取
		$content = $wb->getWBHtml($this->weibo->wb_url, $this->cookieWeibo, $this->cookieCurl);		
		$isLogin = $wb->requiresLogin($content);
		
		if($isLogin){
			throw new \Exception("微博登录失效，请重新授权");
		}
		
		Storage::put($file, $content);
		if(!Storage::exists($file)){
			throw new \Exception("无法储存微博页面");
		}
		return true;
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
			
			$this->gid = $match['1'];
			//第一页评论地址
			$comment = sprintf($this->config['WeiboInfo']['commentUrl'], $this->gid, 1);
			$wb = new WeiboContent();
			//获得评论页内容
			$content = $wb->getWBHtml($comment, $this->cookieWeibo, $this->cookieCurl);
			
			$data = json_decode($content, true);
			$pageData = $this->getWeiboCommnetInfo($data);

			$this->weibo->wb_title = $m['1'];
			$this->weibo->wb_comment_gid = $this->gid;
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
}
