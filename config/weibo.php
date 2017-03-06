<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 微博登陆参数
    |--------------------------------------------------------------------------
    |
    | 新浪认证系统用户名和密码
    |
    |
    */

    'login' => [
    	'USERNAME' => '',
    	'PASSWORD' => '',	
    ],

		
    /*
    |--------------------------------------------------------------------------
    | curl设置参数
    |--------------------------------------------------------------------------
    |
    |
    */
		
    'curl' => [
    	'entry' => 'weibo',
    	'gateway' => '1',
    	'from' => 'null',
    	'savestate' => '7',
    	'useticket' => '1',
    	'pagerefer' => '',
    	'vsnf' => '1',
    	'service' => 'miniblog',
    	'sr' => '1366*768',
    	'encoding' => 'UTF-8',
    	'cdult' => '3',
    	'domain' => 'sina.com.cn',
    	'prelt' => '0',    	
    	'pwencode' => 'rsa2',
    	'url' => 'http://weibo.com/ajaxlogin.php?framelogin=1&callback=parent.sinaSSOController.feedBackUrlCallBack',
    	'returntype' => 'TEXT',
    		
//     	'entry' => 'finance',
//     	'gateway' => '1',
//     	'from' => 'null',
//     	'savestate' => '30',
//     	'qrcode_flag' => 'false',
//     	'useticket' => '0',
//     	'pagerefer' => 'http://i.finance.sina.com.cn/zixuan,stock',
//     	'vsnf' => '1',
//     	'service' => 'finance',
//     	'sr' => '1920*1080',
//     	'encoding' => 'UTF-8',
//     	'cdult' => '3',
//     	'domain' => 'sina.com.cn',
//     	'prelt' => '0',
//     	'returntype' => 'TEXT',	
    ],
	
		
	/**
	 * weibo Cookie储存设置
	 */
	'CookieFile' => [
		'dir' => storage_path('app/wbcookie'),
		//通行证使用
		'sina'	=> storage_path('app/wbcookie')."/cookie_sina.txt",
		//weibo
		'weibo'	=> storage_path('app/wbcookie')."/cookie_weibo.txt",
		//curl获取页面使用
		'curl' => storage_path('app/wbcookie')."/cookie_curl.txt",
			
		//登陆信息，设计一次销毁, 
		//以storage为
		'loginInc' => "wbcookie/config.inc",
		//预登陆配置
		'preLoginInc' => "wbcookie/prelogin.config.inc",
			
	],	

	/**
	 * weibo.cn Cookie储存设置
	 */
	'CookieFileCn' => [
		'dir' => storage_path('app/wbcookie'),
		//通行证使用
		'sina'	=> storage_path('app/wbcookie')."/cookie_cn_sina.txt",
		//weibo
		'weibo'	=> storage_path('app/wbcookie')."/cookie_cn_weibo.txt",
		//curl获取页面使用
		'curl' => storage_path('app/wbcookie')."/cookie_cn_curl.txt",
			
		//登陆信息，设计一次销毁, 
		//weibo.cn预登陆信息
		'loginCnInc' => "wbcookie/cn.config.inc",		
	],	
		
		
	/**
	 * weibo user相关
	 * 参数可能随着微博更新变动
	 * 
	 */
	'WeiboUser' => [
		
		//用户微博地址，根据地址使用正则获得usercard
		//http://weibo.com/pkuyzh?from=hissimilar_home&refer_flag=1005050003_&is_hot=1
		'pregUserFace' => '/weibo\.(com|cn)\/(u\/)?([\w]+)/',
		
		//用户主页
		//该逻辑有问题，早期用户id不能识别，例如使用uc注册用户
		'userWebFace' => 'http://weibo.com/%s',
		
		//抓取地址，cn
		'userFace' => 'http://weibo.cn/%s',
		'userFans' => 'http://weibo.cn/%s/fans',
		'userFollow' => 'http://weibo.cn/%s/follow',
			
	],	

	/**
	 * weibo 
	 * 参数可能随着微博更新变动
	 * 
	 */
	'WeiboInfo' => [
			
		//用户微博接口地址
		//id = domain+uid
		//http://weibo.com/p/aj/v6/mblog/mbloglist?ajwvr=6&domain=100505&profile_ftype=0&is_all=1&page=1&id=1005052885256544
		'weibosUrl' => [ 
							"http://weibo.com/p/aj/v6/mblog/mbloglist?ajwvr=6&domain=%d&profile_ftype=0&is_all=1&page=%d&id=%d",
							"http://weibo.com/p/aj/v6/mblog/mbloglist?ajwvr=6&domain=%d&profile_ftype=0&pagebar=0&is_all=1&page=%d&id=%d&pre_page=%d",
							"http://weibo.com/p/aj/v6/mblog/mbloglist?ajwvr=6&domain=%d&profile_ftype=0&pagebar=1&is_all=1&page=%d&id=%d&pre_page=%d",
						],
		//微博评论接口地址
		//id = mid
		'commentUrl' => "http://weibo.com/aj/v6/comment/big?ajwvr=6&id=%s&page=%d",
			
		//微博的赞
		'likeUrl' => "http://weibo.com/aj/v6/like/big?ajwvr=6&mid=%s&page=%d",
		//评论的赞
		'likeCommentUrl' => "http://weibo.com/aj/like/object/big?ajwvr=6&object_id=%s&object_type=comment&_t=0&page=%d",
		
		//http://weibo.com/aj/v6/mblog/info/big?ajwvr=6&id=4078059135268877&max_id=4081555939339290&page=2&__rnd=1488685383803
		'forwardUrl' => "http://weibo.com/aj/v6/mblog/info/big?ajwvr=6&id=%d&page=%d&__rnd=%s",
			
		//微博标题
		'pregTitle' => '/\<title\>([\s\S]*?)\<\/title\>/',
			
		//获取评论id
		'pregCommentId' => '/\\\"key\=profile_feed&value\=comment\:(\d+)\\\"/',
		
		//页面主人id
		//$CONFIG['oid'];
		'oid' => '/\$CONFIG\[\'oid\'\]\=\'(\d+)\'/',

		//$CONFIG['onick'];
		'onick' => '/\$CONFIG\[\'onick\'\]\=\'(.*)\'/U',

		//$CONFIG['domain'] 用户微博地址使用
		'domain' => '/\$CONFIG\[\'domain\'\]\=\'(\d+)\'/',
		
		 //$CONFIG['page_id'] 用户微博地址使用
		'page_id' => '/\$CONFIG\[\'page_id\'\]\=\'(\d+)\'/',
			
		//用户，转发，粉丝等信息域
		'userInfoView' => '/\<script\>FM\.view\(([\S]+Pl_Core_T8CustomTriColumn__3[\S\s]+)\)\<\/script\>/iU',
			
		//微博 信息域
		'weiboInfoView' => '/\<script\>FM\.view\(([\S]+pl\.content\.weiboDetail\.index[\S\s]+)\)\<\/script\>/iU',
	],	
	
	
	/**
	 * 预登陆url
	 * 新浪通行证随着预登陆系统不同参数也不同
	 */
	'PreLoginUrl' => "http://login.sina.com.cn/sso/prelogin.php?entry=weibo&callback=sinaSSOController.preloginCallBack&su=%s&rsakt=mod&checkpin=1&client=ssologin.js(v1.4.18)&_=%s",
	//'PreLoginUrl' => "https://login.sina.com.cn/sso/prelogin.php?entry=finance&callback=sinaSSOController.preloginCallBack&su=&rsakt=mod&client=ssologin.js(v1.4.18)&_=%s",
		
	//&_=Unix时间戳毫秒级
	'SinaLoginUrl' => "https://login.sina.com.cn/sso/login.php?client=ssologin.js(v1.4.18)&_=%s",

	'WeiboCnLogin' => "https://login.weibo.cn/login/",

];
