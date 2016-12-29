<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Libraries\Classes\WeiboLogin;
use App\Libraries\Contracts\GetWeiboCookie;
use Symfony\Component\DomCrawler\Crawler;

use Storage;

/**
 * 模拟登陆weibo.cn
 * @author daweilang
 *
 */

class AuthorizeCnController extends Controller
{
	
	//设置组名
	public function __construct()
	{
		view()->share('groupName', 'weibo');
		view()->share('routeName', 'authorize');
	}
	
    //
    public function index()
    {
    	$wb = new WeiboLogin();
    	//抓取
    	$content = $wb->loginCn(config('weibo.WeiboCnLogin'));
    	
    	$crawler = new Crawler();
    	$crawler->addHtmlContent($content);
    	
    	$arrayTmp = $crawler->filterXPath('//input')->each(function (Crawler $node, $i) {
    		if(preg_match('/^password\_(\d+)$/' , $node->attr('name') , $m)){
    			return ['password_num' => $m[1]];
    		}
    		if($node->attr('name') != "mobile"){
    			return [$node->attr('name') => $node->attr('value')];
    		}
    	});
    	$dataArray = [];
    	foreach ($arrayTmp as $k => $v){
    		if(!empty($v)){
    			$dataArray = array_merge($dataArray, $v);
    		}
    	}
    	
    	$arrayTmp = $crawler->filterXPath('//img')->each(function (Crawler $node, $i) {
    		if($node->attr('alt') == "请打开图片显示"){
    			return $node->attr('src');
    		}
    	});
    	foreach ($arrayTmp as $v){
    		if(!empty($v)){
    			$dataArray['code'] = $v;
    		}
    	}
    	
    	$dataArray['action'] = $crawler->filter('form')->attr('action');
    	
    	//将登录参数储存到文件
    	$savePath = config('weibo.CookieFileCn.loginCnInc');
    	$bytes = Storage::put($savePath, json_encode($dataArray));
    	if(!Storage::exists($savePath)){
    		return redirect()->back()->withInput()->withErrors('无法设置授权信息');
    	}
    	
    	return view("admin/authorize/browser_cn_login", $dataArray);

    }
  
    
    /**
     * 接收加密后的密码和登录信息，获取cookie
     * @param Request $request
     * @return string
     */
    public function getCookie(Request $request)
    {
    	
    	$preParam = $this->getPreConfig();
    
    	$this->validate($request, [
    			'username' => 'required|max:255',
    			'password' => 'required',
    			'code' => 'required',
    	]);
    	
    	$preParam['mobile']  = $request->get('username');
    	$preParam['password_'.$preParam['password_num']]  = $request->get('password');
    	$preParam['code']  = $request->get('code');
    	
    	if ($this->getCookiePack($preParam)) {
    		return Redirect ( "admin/authorize/seccuss" );
    	} 
    	else {
    		return Redirect ( "admin/authorize/fail" );
    	}
    }
    
    /**
     * 测试访问微博
     * @return 
     */
    public function setTestUrl()
    {
    	return view("admin/authorize/test_cn_url");
    }
    
    
    public function getTestContent(Request $request)
    {
    	$this->validate($request, [
    			'wb_url' => 'required|max:255',
    	]);
    	$url = $request->get('wb_url');
    	//微博cookie
    	$cookieWeibo = config('weibo.CookieFileCn.sina');
    	$cookieCurl =  config('weibo.CookieFileCn.curl');
    	
    	if(!file_exists($cookieWeibo)){
    		return view("admin/error", [ 'error' => '未获得授权信息，请重新登录！']);
    	}
    	
    	$wb = new \App\Libraries\Classes\WeiboContent();
    	$content = $wb->getWBHtml($url, $cookieWeibo, $cookieCurl);
    	//需要根据返回结果判断
//     	if(empty($content)){
//     		return view("admin/error", [ 'error' => '未获得授权信息，请重新登录！']);
//     	}
    	return view("admin/authorize/test_show", ['html'=>$content]);
    }
    
    
    /**
     * 获得预登陆配置
     */
    private function getPreConfig()
    {
	    //获得预登陆配置
	    $savePath = config('weibo.CookieFileCn.loginCnInc');
	    if(!Storage::exists($savePath)){
	    	return false;
	    }
	    return json_decode(Storage::get($savePath), true);
    }
    
    
    /**
     * 封装登录
     */
    private function getCookiePack($preParam)
    {
    	$getCookie = new GetWeiboCookie();
    	try {
    		if ($getCookie->getCnCookie ( $preParam )) {
    			return true;
    		} else {
    			return false;
    		}
    	}
    	catch (\Exception $e){
    		//abort(505, $e->getMessage());
    		return false;
    	}
    }
    
}
