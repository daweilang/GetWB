<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Wb_complete;

use App\Jobs\GetUserCompleteJob;

// use App\Libraries\Contracts\GetWeiboInfo;
// use App\Jobs\GetWeiboJob;
// use App\Jobs\GetWeiboAllJob;
// use App\Libraries\Contracts\GetWeiboInfo;

/**
 * 综合分析微博用户数据，从用户所有微博到微博的评论，赞，以及用户的粉丝
 * @author daweilang
 * status的几种状态， 0：设置，1：完成信息获取，2：重新设置，3：获取失败
 */

class CompleteController extends Controller
{
	
	//执行延时
	public $delay = 10;
	//对了名称开关
	public $jobName = FALSE;
	
	//设置组名
	public function __construct()
	{
		view()->share('groupName', 'complete');		
		view()->share('routeName', 'index');
		view()->share('path', 'admin/complete');
		
		//获得全局延时时间设置
		if(config('queue.delay')){
			$this->delay = config('queue.delay');
		}
	}
	
    //
    public function index()
    {
    	return view('admin/complete/index')->withWeibos(Wb_complete::orderBy('uid', 'desc')->paginate(10));
    }
    
    public function create()
    {
    	return view('admin/complete/create');
    }
    
    public function store(Request $request)
    {
    	// 数据验证
    	$this->validate($request, [
    			'user_url' => 'required|max:255', 
    	]);
    	$wb_url = $request->get('user_url'); 
    
    	//判断输入链接是否为用户格式
    	if(preg_match(config('weibo.WeiboUser.pregUserFace'), $wb_url, $m)){
    		$usercard = $m['3'];
    	}
    	else{
    		return redirect()->back()->withInput()->withErrors('输入微博用户地址错误！');
    	}
    	$this->setJob($usercard);
    	return view('admin/msg', ['notice'=>'已经设置后台任务获取微博用户信息，请稍后访问']);
    }
    
    
    public function edit($id)
    {
    	return view('admin/complete/edit')->withWeibo(Wb_complete::find($id));
    }
 
    
    public function update(Request $request, $id)
    {
    	// 数据验证
    	$this->validate($request, [
    			'user_url' => 'required|max:255',
    	]);
    	$wb_url = $request->get('user_url');
    	
    	//判断输入链接是否为用户格式
    	if(preg_match(config('weibo.WeiboUser.pregUserFace'), $wb_url, $m)){
    		$usercard = $m['3'];
    	}
    	else{
    		return redirect()->back()->withInput()->withErrors('输入微博用户地址错误！');
    	}
    	
    	$weibo = Wb_complete::find($id);
    	$weibo->status = '2'; //再次分析
    	 
    	if($weibo->save()){   		
			$this->setJob($usercard);
    		return view('admin/msg', ['notice'=>'已经设置后台任务获取微博用户信息，请稍后访问']);
    	}
    	else{
    		return redirect()->back()->withInput()->withErrors('更新失败');
    	}
    }
    
    private function setJob($usercard)
    {
    	//将任务添加到队列，获得微博信息
    	if($this->jobName){
    		$job = (new GetUserCompleteJob($usercard))->onQueue('GetUserComplete')->delay($this->delay);
    	}
    	else{
    		$job = (new GetUserCompleteJob($usercard))->delay($this->delay);
    	}
    	$this->dispatch($job);
    }
    
}
