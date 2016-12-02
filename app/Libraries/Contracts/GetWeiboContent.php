<?php

/**
 *  GetWeiboHtml.php 抓取微博内容封装
 *
 * @copyright			(C) daweilang
 * @license				https://github.com/daweilang/
 *
 */


namespace App\Libraries\Contracts;

use App\Libraries\Classes\WeiboLogin;
use Storage;


class GetWeiboContent
{
	private $cookieWeibo;
	private $cookieGet;
	
	
	public function __construct()
	{
		//微博cookie
		$this->cookieWeibo = storage_path()."/app/wbcookie/cookie_weibo.txt";
		$this->cookieGet =  storage_path()."/app/wbcookie/cookie_curl.txt";
	}
	
	public function getWeiboHtml($url)
	{
		$wbLogin = new WeiboLogin();
		//测试抓取
		$content = $wbLogin->getWBHtml($url, $this->cookieWeibo, $this->cookieGet);
		file_put_contents(storage_path()."/app/wbcookie/weibo_explain", $content);
		return true;
	}
	
}
