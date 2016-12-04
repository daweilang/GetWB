<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::get('/home', 'HomeController@index');

Route::group(['middleware' => 'auth', 'namespace' => 'Admin', 'prefix' => 'admin' ], function (){
	
	Route::get('/', 'HomeController@index');
	Route::resource('comment', 'CommentController');
	Route::resource('getCookie', 'GetCookieController');
	
// 	Route::resource('weibo', 'WeiboController');

	Route::resource('weibo', 'WeiboController');
	Route::get('weibo/getwbcoo/{id}', 'WeiboController@getWeiboCookie');
	Route::get('weibo/getWBHtml', 'WeiboController@getWeiboHtml');
	
	//获得微博授权相关
	Route::get('authorize', 'AuthorizeController@index');
	Route::post('authorize/setConfig', 'AuthorizeController@setConfig');
	Route::get('authorize/getPreParam', 'AuthorizeController@getPreParam');
	Route::get('authorize/browserLogin', 'AuthorizeController@browserLogin');
	Route::post('authorize/getCookie', 'AuthorizeController@getCookie');
	
	//返回提示成功信息
	Route::get('authorize/seccuss', function(){return view('admin/weibo/seccuss',['groupName' => 'weibo']);});
	Route::get('authorize/fail', function(){return view('admin/weibo/fail',['groupName' => 'weibo']);});
});

Route::get('/admin/authorize/getRsaPwd', 'Admin\AuthorizeController@getRsaPwd');





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