<?php

namespace App\Libraries\Classes;

/**
 * 获得weibo内容基础类
 * @author daweilang
 */

use Symfony\Component\DomCrawler\Crawler;

class WeiboContent extends CurlHandler
{
	
	/**
	 * 抓取微博
	 * @param unknown $url
	 * @param unknown $cookieFile2
	 * @param unknown $cookieFile3
	 * @return mixed
	 */
	public function getWBHtml($url, $cookieFile2, $cookieFile3)
	{	
		$curlConfig = [
				
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				//包含 cookie 数据的文件名
				CURLOPT_COOKIEFILE => $cookieFile2,
				//连接结束后，比如，调用 curl_close 后，保存 cookie 信息的文件。
				CURLOPT_COOKIEJAR => $cookieFile3,
				//TRUE 时将会根据服务器返回 HTTP 头中的 "Location: " 重定向
				CURLOPT_FOLLOWLOCATION => 1,
		];
		return $this->useCurl($url, $curlConfig);
	}
	

	/**
	 * 抓取手机微博
	 * @param unknown $url
	 * @param unknown $cookieFile2
	 * @param unknown $cookieFile3
	 * @return mixed
	 */
	public function getFansPage($url, $data, $cookieFile2, $cookieFile3)
	{	
		$curlConfig = [
				//TRUE 时会发送 POST 请求
				CURLOPT_POST => true,
				
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				
				//设置 HTTP 头字段的数组
				CURLOPT_HTTPHEADER => array (
						'Host' => 'weibo.cn',
						'User-Agent' => 'Mozilla/5.0 (iPad; CPU OS 6_1_3 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10B329 Safari/8536.25',
						'Accept' => '*/*',
						'Accept-Language' => 'zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
						'Accept-Encoding' => 'gzip, deflate',
						'Referer' => '',
						'Connection' => 'keep-alive'
				),				
				CURLOPT_POSTFIELDS => http_build_query($data),
				//包含 cookie 数据的文件名
				CURLOPT_COOKIEFILE => $cookieFile2,
				//连接结束后，比如，调用 curl_close 后，保存 cookie 信息的文件。
				CURLOPT_COOKIEJAR => $cookieFile3,
				//TRUE 时将会根据服务器返回 HTTP 头中的 "Location: " 重定向
				CURLOPT_FOLLOWLOCATION => 1,
		];
		return $this->useCurl($url, $curlConfig);
	}
	
	/**
	 * 根据返回的头判断是否需要登录
	 * @param unknown $html
	 */
	public function requiresLogin($html){
		$crawler = new Crawler();
		$crawler->addHtmlContent($html,'GBK');
		//返回新浪通行证
		$title = $crawler->filter('title')->text();
		if($title == '新浪通行证'){
			return true;
		}
		else {
			return false;	
		}		
// 		return $crawler->filter('body')->text();
	}
	
}