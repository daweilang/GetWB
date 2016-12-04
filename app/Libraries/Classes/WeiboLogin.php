<?php

namespace App\Libraries\Classes;

/**
 * 模拟登陆weibo基础类
 * @author daweilang
 */


class WeiboLogin extends CurlHandler
{
	
	/**
	 * 获得预登陆请求，get请求
	 *
	 * @param string $url
	 * @param string $request_cookie
	 */
	public function loginPre($url) {

		$curlConfig = [	
				//设定 HTTP 请求中"Cookie: "部分的内容
				CURLOPT_COOKIE => '',
				//启用时会将头文件的信息作为数据流输出
				CURLOPT_HEADER => 0,
				//设置 HTTP 头字段的数组
				CURLOPT_HTTPHEADER => array (
					'Host' => 'login.sina.com.cn',
					'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0',
					'Accept' => '*/*',
					'Accept-Language' => 'zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
					'Accept-Encoding' => 'gzip, deflate',
					'Referer' => 'http://login.sina.com.cn/',
					'Connection' => 'keep-alive'
				),
				//TRUE 时将会根据服务器返回 HTTP 头中的 "Location: " 重定向
				CURLOPT_FOLLOWLOCATION => 1,		
		];
		return $this->useCurl($url, $curlConfig);
	}
	
	/**
	 * 登录新浪通行证使用
	 * @param unknown $url
	 * @param unknown $data
	 * @param unknown $cookieFile
	 * @return mixed
	 */
	public function loginSina($url, $data, $cookieFile)
	{
		$curlConfig = [
				//TRUE 时会发送 POST 请求
				CURLOPT_POST => true,
				//设置 HTTP 头字段的数组
				CURLOPT_HTTPHEADER => array('Accept-Language: zh-cn','Connection: Keep-Alive','Cache-Control: no-cache'),
				//true 禁止 cURL 验证对等证书, 验证证书
// 				CURLOPT_SSL_VERIFYPEER => true,
				//设置为 1 是检查服务器SSL证书中是否存在一个公用名(common name)
// 				CURLOPT_SSL_VERIFYHOST => false,
				//全部数据使用HTTP协议中的 "POST" 操作来发送。
				CURLOPT_POSTFIELDS => http_build_query($data),
				//连接结束后，比如，调用 curl_close 后，保存 cookie 信息的文件。
				CURLOPT_COOKIEJAR => $cookieFile,
		];
		return $this->useCurl($url, $curlConfig);
	}

	
	/**
	 * 登录新浪微博 
	 * @param unknown $url
	 * @param unknown $cookieSina
	 * @param unknown $cookieWeibo
	 * @return mixed
	 */
	public function loginWeibo($url, $cookieSina, $cookieWeibo )
	{
		$curlConfig = [
				//TRUE 时会发送 POST 请求
				CURLOPT_POST => true,
				//设置 HTTP 头字段的数组
				CURLOPT_HTTPHEADER => array('Accept-Language: zh-cn','Connection: Keep-Alive','Cache-Control: no-cache'),

				CURLOPT_HEADER => 0,				
				CURLOPT_SSL_VERIFYPEER => true,
				CURLOPT_SSL_VERIFYHOST => 2,
				CURLOPT_COOKIEFILE => $cookieSina,
				CURLOPT_COOKIEJAR => $cookieWeibo,
		];
		return $this->useCurl($url, $curlConfig);
	}
	
	
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
	
}