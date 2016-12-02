<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Libraries\Classes\WeiboLogin;

use Config;
use Storage;

class GetCookieController extends Controller
{
	public function index()
	{
		$config = Config::get('weibo');
		
		$loginData = $config['curl'];
		
		//用户名密码
		$loginData['su'] = base64_encode($config['login']['USERNAME']);
		$loginData['sp'] = $config['login']['PASSWORD'];
		
		//登录新浪通行证
		$wbLogin = new WeiboLogin;
		
		$cookieName  = storage_path()."/app/wbck/cookie.txt";
		
// 		include_once app_path().'/Libraries/function/helpers.php';

// 		$login = json_decode($wbLogin->loginSina($config['SinaLogin'].dw_microtime(), $loginData, $cookieName),true);
		$login = json_decode($wbLogin->loginExample($config['SinaLogin'], $loginData, $cookieName),true);

		echo $login['crossDomainUrlList'][0];
		
		return "<br>".date("Y-m-d H:i:s");
		//获取微博cookie
		$wbLogin->loginWeibo($login['crossDomainUrlList'][0], $cookieName, $cookieName);
		
		echo "<br>".date("Y-m-d H:i:s");
		
		$url = "http://weibo.com/1563926367/EcN8BcyME?type=comment#_rnd1477219631405";
		//获得评论链接
		##$comment = "http://weibo.com/aj/v6/comment/big?ajwvr=6&id=4030488252993648";
		
		$content = $wbLogin->getWBHtml($url, $cookieName, storage_path()."/app/wbck/cookie3.txt");
		
		//分析 url \"key=feed_trans_weibo&value=comment:4022938104139981\"
		echo $content."<br>";
		
// 		preg_match('/\\\"key\=profile_feed&value\=comment\:(\d+)\\\"/', $content , $match);
// 		$comment = "http://weibo.com/aj/v6/comment/big?ajwvr=6&id=".$match[1]."&page=1";
// 		echo $comment."<br>";
// 		$content = $wbLogin->getWBHtml($comment, $cookieName, $cookieName);
		
// 		$savePath = 'wbcookie/weibo_explain';
// 		$bytes = Storage::put($savePath, $content);
// 		if(!Storage::exists($savePath)){
// 			exit('保存文件失败！');
// 		}
// 		header("Content-Type: ".Storage::mimeType($savePath));
// 		echo Storage::get($savePath);
// 		echo "<br>";
		return "<br>".date("Y-m-d H:i:s");
	}
	
}
