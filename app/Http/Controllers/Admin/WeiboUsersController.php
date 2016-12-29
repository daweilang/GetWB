<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Wb_user;
use App\Jobs\GetUserInfoJob;

use App\Libraries\Contracts\GetWeiboInfo;

/**
 * 抓取微博用户信息
 * @author daweilang
 */

class WeiboUsersController extends Controller
{
	
	//设置组名
	public function __construct()
	{
		view()->share('groupName', 'weibo');
		view()->share('routeName', 'users');
		view()->share('path', 'admin/users');
	}
	
    //
    public function index()
    {
    	return view('admin/users/index')->withWeibos(Wb_user::paginate(15));
    }
    
    public function create()
    {
    	return view('admin/users/create');
    }
    
    public function store(Request $request)
    {
    	// 数据验证
    	$this->validate($request, [
    		'wb_url' => 'required', // 必填
    	]);
    
    	$wb_url = $request->get('wb_url');
    	//判断输入链接是否为用户格式
    	if(preg_match(config('weibo.WeiboUser.pregUserFace'), $wb_url, $m)){
    		$usercard = $m['3'];
    	}
    	else{
    		return redirect()->back()->withInput()->withErrors('输入微博地址错误！');
    	}  	
		//将任务添加到队列，获得微博信息
    	$job = (new GetUserInfoJob($usercard))->delay(10);
//     	$job = (new GetUserInfoJob($usercard))->onQueue('GetUserInfo')->delay(10);
    	$this->dispatch($job);
    	return view('admin/msg', ['notice'=>'已经设置后台任务获取微博用户信息，请稍后访问']);
    }
    
    
    public function edit($id)
    {
    	return view('admin/users/edit')->withWeibo(Wb_user::find($id));
    }
 
    
    public function update(Request $request, $id)
    {
    	$this->validate($request, [
    			'wb_url' => 'required',
    	]);
    	
    	$wb_url = $request->get('wb_url');
    	//判断输入链接是否为用户格式
    	if(preg_match(config('weibo.WeiboUser.pregUserFace'), $wb_url, $m)){
    		$usercard = $m['3'];
    	}
    	else{
    		return redirect()->back()->withInput()->withErrors('输入微博地址错误！');
    	}
    	$weibo = Wb_user::find($id);
    	$weibo->wb_status = '2'; //再次分析
    	 
    	//将任务添加到队列，获得微博信息
    	$job = (new GetUserInfoJob($usercard))->delay(10);
    //     	$job = (new GetUserInfoJob($usercard))->onQueue('GetUserInfo')->delay(10);
    	$this->dispatch($job);
    	return view('admin/msg', ['notice'=>'已经设置后台任务获取微博用户信息，请稍后访问']);
    }
    
}
