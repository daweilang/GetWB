<?php

/* ================== Homepage ================== */
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::auth();


/*
 |--------------------------------------------------------------------------
 | Admin Application Routes
 |--------------------------------------------------------------------------
 */

Route::group(['middleware' => 'auth', 'namespace' => 'Admin', 'prefix' => 'admin' ], function (){
	
	Route::get('/', 'HomeController@index');
	
	/* ================== test ================== */
	Route::get('test', 'TestController@exampleTest');
	Route::get('test/redis', 'TestController@exampleRedis');
	Route::get('crawler', 'TestController@exampleCrawler');
	
	/* ================== weibo ================== */
	Route::resource('weibo', 'WeiboInfoController');
	Route::get('weibo/test/{mid}', 'WeiboInfoController@exampleTest');

	/* ================== users ================== */
	Route::resource('users', 'WeiboUsersController');
// 	Route::resource('users/test/{mid}', 'WeiboUsersController@exampleTest');

	/* ================== fans ================== */
	Route::get('fans/{uid}', 'WeiboFansController@index');
	Route::get('fans/settingJob/{uid}', 'WeiboFansController@settingJob');
	
	/* ================== 微博评论任务 ================== */
	Route::get('commentJob/{mid}', 'CommentJobController@index');
	Route::get('commentJob/setting/{mid}', 'CommentJobController@Setting');
	Route::get('commentJob/settingJob/{mid}', 'CommentJobController@settingJob');
	
	/* ================== 获得微博授权相关 ================== */
	Route::get('authorize', 'AuthorizeController@index');
	Route::post('authorize/setConfig', 'AuthorizeController@setConfig');
	Route::get('authorize/getPreParam', 'AuthorizeController@getPreParam');
	Route::get('authorize/browserLogin', 'AuthorizeController@browserLogin');
	Route::post('authorize/getCookie', 'AuthorizeController@getCookie');
	Route::get('authorize/getRsaPwd', 'AuthorizeController@getRsaPwd');
	Route::get('authorize/test', 'AuthorizeController@setTestUrl');
	Route::post('authorize/getTestContent', 'AuthorizeController@getTestContent');

	/* ================== 获得手机微博授权相关 ================== */
	Route::get('authorizeCn', 'AuthorizeCnController@index');
	Route::post('authorizeCn/getCookie', 'AuthorizeCnController@getCookie');
	Route::get('authorizeCn/test', 'AuthorizeCnController@setTestUrl');
	Route::post('authorizeCn/getTestContent', 'AuthorizeCnController@getTestContent');
	
	
	/* ================== 返回提示成功信息 ================== */
	Route::get('authorize/seccuss', function(){return view('admin/weibo/seccuss',['groupName' => 'admin']);});
	Route::get('authorize/fail', function(){return view('admin/weibo/fail',['groupName' => 'admin']);});

	
	/* ================== 完全分析用户数据系统 ================== */
	Route::resource('complete', 'CompleteController');
	Route::get('complete/{uid}/weibos', 'CompleteController@weibos');
	Route::get('complete/settingWB/{uid}', 'CompleteController@settingWB');
	Route::get('complete/setGetAll/{uid}/{mid?}', 'CompleteController@setGetAll');
	Route::get('complete/test/{uid}', 'CompleteController@exampleTest');
	
	
	/* ================== 返回提示信息 ================== */
	Route::get('message/{status?}/{msg?}', function($status=null, $msg=''){		
			if($msg){
				$msg = config("message.route.$msg");
			}
			else{
				$msg = "任务设置";
			}
			switch ($status) {
				case 0:
					$status = "失败";
					break;
				case 1:
					$status = "成功";
					break;
				default:
					$status = "";					
			}
			return view('admin/msg',['groupName' => 'admin', 'notice' => "$msg $status"]);
		});
	
});



Route::get('/que',function (){                                                     // route from <server_ip>/que
	$queue = Queue::push('LogMessage', array('message' => 'Time: '.time()));               // this will push job in queue
	// OR
// 	$queue = Queue::later($delay,'LogMessage', array('message' => 'Time: '.time()));     // this will push job in queue after $delay
// 	sleep(5);    //you can add delay here too
	print_r(" ".$queue." ".time());            //prints queue_id and time stamp
});



class LogMessage{                                                                //bad practice to deploy code here :p
		public function fire($job,$data){                                         //takes data and performs action.			 
			File::append(app_path().'/queue.txt',$data['message'].PHP_EOL);
			$job->delete();
		}
	}