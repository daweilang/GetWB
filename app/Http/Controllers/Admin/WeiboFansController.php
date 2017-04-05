<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wb_user;
use App\Models\Wb_fans;

use App\Jobs\GetFansJob;

/**
 * 抓取微博用户粉丝系统
 * @author daweilang
 */

class WeiboFansController extends Controller
{
	
	//设置组名
	public function __construct()
	{
		view()->share('groupName', 'weibo');
		view()->share('routeName', 'user');
		view()->share('path', 'admin/fans');
	}
	
    //
    public function index($uid)
    {
    	$userinfo = Wb_user::where('uid', $uid)->first();
    	//粉丝池达到一定量级时采用其他获取方式
    	$data = Wb_fans::where('uid', $uid)->paginate(15);
    	return view('admin/fans/user_fans_job', ['userinfo'=>$userinfo, 'fans'=>$data]);
    }
    
    
    /**
     * 设置获取粉丝的任务，页面设置
     * 设计原理，获取weibo.cn的粉丝页面和关注页面进行分析，由于微博限制，每个用户只能获得200个关注和粉丝，
     * @param unknown $uid 用户id
     * @return
     */
    public function settingJob($uid)
    {
    	$job = (new GetFansJob($uid))->delay(10);
    	//多进程时候使用命名
    	//$job = (new GetFansJob($uid))->onQueue('GetFansJob')->delay(10);
    	$this->dispatch($job);
    	return redirect('admin/message/3/setCommentJobJob');
    }
}
