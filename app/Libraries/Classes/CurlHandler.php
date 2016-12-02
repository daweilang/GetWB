<?php

namespace App\Libraries\Classes;

use Storage; 

/**
 * curl封装基础类
 * @author daweilang
 *
 */


abstract class CurlHandler
{
	
	const VERSION = '1.0.1';
	
	//页面执行时间
	private  $defaultTimeout = "60";
	
	public function __construct()
	{
		if (!extension_loaded('curl')) {
			throw new \ErrorException('cURL library is not loaded');
		}
	}
	
	
	/**
	 * 封装curl
	 * 主要用于 curl_setopt 参数传递
	 **/
	protected function useCurl($url, $data, $timeOut = FALSE)
	{
		
		if($timeOut && is_int($timeOut)){
			$this->setTimeout($timeOut);
		}
		
		//curl_setopt默认参数
		$dataInit = [
				CURLOPT_URL => $url,
				//设置 HTTP 头字段的数组
				CURLOPT_HTTPHEADER => array('Accept-Language: zh-cn','Connection: Keep-Alive','Cache-Control: no-cache'),
				//伪造浏览器信息
				CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.79 Safari/537.1",
				//将curl_exec()获取的信息以字符串返回
				CURLOPT_RETURNTRANSFER => 1,
				//在尝试连接时等待的秒数。设置为0，则无限等待。
				CURLOPT_CONNECTTIMEOUT => $this->defaultTimeout,
		];
		//合并数组
		foreach ($data as $k => $v){
			$dataInit[$k] = $v;
		}
	
		//初始化
		$ch = curl_init();
	
		//加载参数
		foreach ($dataInit as $k => $v){
			curl_setopt($ch, $k, $v);
		}
	
		//执行
		$data = curl_exec($ch);
		
		if (curl_errno($ch) > 0) {
			$savePath = 'curl.log';
			Storage::put($savePath, date("Y-m-d H:i:s")." CURL ERROR:$url " . curl_error($ch));
		}
		
		$info = curl_getinfo($ch);
		curl_close($ch);
		return $data;
	}
	
	
	/**
	 * 在尝试连接时等待的秒数。设置为0，则无限等待。
	 * @param unknown $timeOut
	 */
	private function setTimeout($timeOut)
	{
		if($timeOut){
			$this->defaultTimeout = $timeOut;
		}
	}
	
	
	/**
	 * curl示例说明
	 * @param $url 
	 * @param 登录参数 $data
	 * @param 存储的 $cookieFile 地址
	 * @return mixed
	 **/	
	private function curlExample($url, $data, $cookieFile)
	{
		
		//初始化
		$ch = curl_init();
	
		//需要获取的 URL 地址
		curl_setopt($ch, CURLOPT_URL, $url);
	
		//将curl_exec()获取的信息以字符串返回
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		//TRUE 时会发送 POST 请求
		curl_setopt($ch, CURLOPT_POST, 1);
	
		//true 禁止 cURL 验证对等证书, 验证证书
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
		//设置为 1 是检查服务器SSL证书中是否存在一个公用名(common name)
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		//在尝试连接时等待的秒数。设置为0，则无限等待。
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->defaultTimeout);
	
		//全部数据使用HTTP协议中的 "POST" 操作来发送。
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	
		//连接结束后，比如，调用 curl_close 后，保存 cookie 信息的文件。
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);

		//连接结束后，比如，调用 curl_close 后，保存 cookie 信息的文件。
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);

		//执行
		$data = curl_exec($ch);
		// 		$info = curl_getinfo($ch);
		curl_close($ch);
		return $data;
	}
	
}