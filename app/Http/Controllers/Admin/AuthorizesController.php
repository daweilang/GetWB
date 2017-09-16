<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Libraries\Contracts\GetWeiboCookie;

use Storage;


/**
 * 授权用户管理系统
 * @author daweilang
 *
 */
class AuthorizesController extends Controller
{
	
	//设置组名
	public function __construct()
	{
		view()->share('groupName', 'authorize');
		view()->share('routeName', 'authorize');
	}
	
    //
    public function index()
    {
    	###判断cookie是否有效
    	return view("admin/authorizes/index");
    }
    
    //将用户名和密码录入临时文件，
    public function setConfig(Request $request)
    {
    	$this->validate($request, [
    			'username' => 'required|max:255',
    			'password' => 'required',
    	]);
    	
    	$content = [
    			'USERNAME' => base64_encode($request->get('username')),
    			'PASSWORD' => $request->get('password'),
    	];
    	
    	$savePath = config('weibo.CookieFile.loginInc');
    	$bytes = Storage::put($savePath, json_encode($content));
    	if(!Storage::exists($savePath)){
    		return redirect()->back()->withInput()->withErrors('无法设置授权信息');
    	}   	
    	return view("admin/authorize/set_config");
    }
    
    
    /**
     * 根据配置文件获得预登陆参数
     * 跳转到密码加密页
     */
    public function getPreParam()
    {
    	try {
    		$getCookie = new GetWeiboCookie();
    		//获得配置
    		$getCookie->getConfig();
    		//获得预登录参数，跳转到预登陆地址
    		$getCookie->getPreUrl();
    	}
    	catch (\Exception $e){
    		abort(504,$e->getMessage());
    	}
    	return Redirect("/admin/authorize/getRsaPwd");
    }

    
    /**
     * 根据传入参数，使用js算法获得加密后的密码
     */
    public function getRsaPwd(Request $request)
    {	
    	//获得预登陆配置
    	$savePath = config('weibo.CookieFile.preLoginInc');
    	if(!Storage::exists($savePath)){
    		return false;
    	}
    	$preParam = json_decode(Storage::get($savePath), true);
    	
    	if(empty($preParam['servertime']))
      	{
       		return "参数错误!";
       	}
       	else{
       		$getCookie = new GetWeiboCookie();
       		$preParam['sp'] = $getCookie->getSp();
       		
       		//python的处理方式，rsa.PublicKey(rsaPubkey, 65537) #创建公钥无法用php实现
       		//     		RSAKey = rsa.PublicKey(rsaPubkey, 65537) #创建公钥
       		//     		codeStr = str(servertime) + '\t' + str(nonce) + '\n' + str(password) #根据js拼接方式构造明文
       		//     		pwd = rsa.encrypt(codeStr, RSAKey)  #使用rsa进行加密
       		
    		header('Content-type:text/html;charset=utf-8');
    		echo "<script type='text/javascript' src='/js/jquery-1.10.2.min.js'></script>\n";
    		echo "<script type='text/javascript' src='/js/ssologin.js'></script>";
    		echo <<<EOT
<script type="text/javascript">
	function getpass(pwd,servicetime,nonce,rsaPubkey){
		var RSAKey=new sinaSSOEncoder.RSAKey();
		RSAKey.setPublic(rsaPubkey,'10001');
		var password=RSAKey.encrypt([servicetime,nonce].join('\\t')+'\\n'+pwd);
		return password;
	}
    document.write('微博密码正在加密！！！');
 	var encrpt = getpass('{$preParam['sp']}', '{$preParam['servertime']}', '{$preParam['nonce']}', '{$preParam['pubkey']}' );
// 	document.write(encrpt);
	window.location.href='/admin/authorize/browserLogin/?sp='+encrpt;
</script>"
EOT;
     		return ;
     	}
    }
    
    
    /**
     * 验证码提交页
     * @param Request $request
     * @return 
     */
    public function browserLogin(Request $request)
    {
    	$preParam = $this->getPreConfig();
    	if(empty($preParam))
    	{
    		return "参数错误!";
    	}
    	$preParam['sp'] = $request->get('sp');
    	
    	if(!isset($preParam['showpin'])){
    		$preParam['showpin'] = 1;
    	}    	
    	if($preParam['showpin'] == 1){    		
    		$randInt = rand(pow(10,(8-1)), pow(10,8)-1);
    		$preParam['doorImg'] = "http://login.sina.com.cn/cgi/pin.php?r={$randInt}&s=0&p={$preParam['pcid']}";
			return view ( "admin/authorize/browser_login", $preParam );
		} 
		else {
			// ##如果没有图片验证码
			$preParam ['door'] = '';
			if ($this->getCookiePack($preParam)) {
				return Redirect ( "admin/authorize/seccuss" );
			} else {
				return Redirect ( "admin/authorize/fail" );
			}
    	}
    }
    
    
    /**
     * 接收加密后的密码和登录信息，获取cookie
     * @param Request $request
     * @return string
     */
    public function getCookie(Request $request)
    {
    	$preParam = $this->getPreConfig();
    	if(empty($preParam))
    	{
    		return "参数错误!";
    	}
    	
    	$preParam['sp']  = $request->get('sp');
    	$preParam['door']  = $request->get('door');
    	
    	if(empty($preParam['sp']))
    	{
    		return "参数错误!";
    	}
    	
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
    	return view("admin/authorize/test_url");
    }
    
    
    public function getTestContent(Request $request)
    {
    	$this->validate($request, [
    			'wb_url' => 'required|max:255',
    	]);
    	$url = $request->get('wb_url');
    	//微博cookie
    	$cookieWeibo = config('weibo.CookieFile.weibo');
    	$cookieCurl =  config('weibo.CookieFile.curl');
    	
    	if(!file_exists($cookieWeibo)){
    		return view("admin/error", [ 'error' => '授权信息不存在，请重新登录！']);
    	}
    	
    	$wb = new \App\Libraries\Classes\WeiboContent();
    	$content = $wb->getWBHtml($url, $cookieWeibo, $cookieCurl);
    	//需要根据返回结果判断
    	if(empty($content)){
    		return view("admin/error", [ 'error' => '未获得授权信息，请重新登录！']);
    	}
    	return view("admin/authorize/test_show", ['html'=>$content]);
    }
    
    
    /**
     * 获得预登陆配置
     */
    private function getPreConfig()
    {
	    //获得预登陆配置
	    $savePath = config('weibo.CookieFile.preLoginInc');
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
    		if ($getCookie->getCookie ( $preParam )) {
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
