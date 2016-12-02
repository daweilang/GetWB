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
//     	'entry' => 'sso',
//     	'savestate' => '30',
//     	'prelt' => '0',
//     	'sr' => '1920*1080',
    	'entry' => 'account',
    	'gateway' => '1',
    	'from' => 'null',
    	'savestate' => '0',
    	'useticket' => '0',
    	'pagerefer' => '',
    	'vsnf' => '1',
    	'service' => 'sso',
    	'sr' => '1366*768',
    	'encoding' => 'UTF-8',
    	'cdult' => '3',
    	'domain' => 'sina.com.cn',
    	'prelt' => '51',
    	'returntype' => 'TEXT',
    	'pwencode' => 'rsa2',
    ],
		
	//&_=Unix时间戳毫秒级
	'SinaLoginUrl' => "https://login.sina.com.cn/sso/login.php?client=ssologin.js(v1.4.18)&_=%s",
	
	// 预登陆url
	'PreLoginUrl' => "http://login.sina.com.cn/sso/prelogin.php?entry=account&callback=sinaSSOController.preloginCallBack&su=%s&rsakt=mod&client=ssologin.js(v1.4.15)&_=%s", 
		

];
