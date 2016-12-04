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
		
	//&_=Unix时间戳毫秒级
	'SinaLoginUrl' => "https://login.sina.com.cn/sso/login.php?client=ssologin.js(v1.4.18)&_=%s",
	
	// 预登陆url
	'PreLoginUrl' => "http://login.sina.com.cn/sso/prelogin.php?entry=weibo&callback=sinaSSOController.preloginCallBack&su=%s&rsakt=mod&checkpin=1&client=ssologin.js(v1.4.18)&_=%s",
// 	'PreLoginUrl' => "https://login.sina.com.cn/sso/prelogin.php?entry=finance&callback=sinaSSOController.preloginCallBack&su=&rsakt=mod&client=ssologin.js(v1.4.18)&_=%s"
		

];
