<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Wb_complete;
use App\Models\Wb_user_weibo;

use App\Jobs\GetUserCompleteJob;
use App\Jobs\GetCompleteWBJob;


/**
 * 综合分析微博用户数据，从用户所有微博到微博的评论，赞，以及用户的粉丝
 * @author daweilang
 * status的几种状态， 0：设置，1：完成信息获取，2：重新设置，3：获取失败
 */

class CompleteController extends Controller
{
	
	//执行延时
	public $delay = 10;
	//队列名称开关
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
    	$userinfo = Wb_complete::find($id);
    	$userinfo['face'] = "http://weibo.com/u/".$userinfo['uid'];
    	return view('admin/complete/edit', ['user' => $userinfo]);
    }
 
    
    public function update(Request $request, $id)
    {
    	// 数据验证
    	
    	$weibo = Wb_complete::find($id);
    	$weibo->status = '2'; //再次分析
    	 
    	if($weibo->save()){   		
			$this->setJob($weibo->usercard);
    		return view('admin/msg', ['notice'=>'已经设置后台任务获取微博用户信息，请稍后访问']);
    	}
    	else{
    		return redirect()->back()->withInput()->withErrors('更新失败');
    	}
    }
    
    
    /**
     * 获得用户所有微博列表
     * @param unknown $id
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function weibos($id)
    {
    	$userinfo = Wb_complete::find($id);
    	$userinfo['face'] = "http://weibo.com/u/".$userinfo['uid'];
    	
    	$weibos = Wb_user_weibo::where("uid",$userinfo['uid'])->paginate(15);
    	$count = Wb_user_weibo::where("uid",$userinfo['uid'])->count();
    	
    	return view('admin/complete/weibos', ['userinfo' => $userinfo, 'weibos' => $weibos, 'count'=>$count]);
    }
    
    
    /**
     * 设置获取全部微博任务，页面设置
     * 设计原理，获取weibo.cn的页面分析，
     * @param unknown $uid 用户id
     * @return
     */
    public function settingWB($uid)
    {
    	if($this->jobName){
    		//多进程时候使用命名
    		$job = (new GetCompleteWBJob($uid))->onQueue('GetCompleteWBJob')->delay($this->delay);
    	}
    	else{
    		$job = (new GetCompleteWBJob($uid))->delay($this->delay);
	    	
    	}
    	$this->dispatch($job);
    	return redirect('admin/message/3/setCommentJobJob');
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
    
    public function test($uid){
    	    $userinfo = Wb_complete::find($uid);
    	   	$weibos = new \App\Libraries\Contracts\GetUserCompleteWB($userinfo);
    	   	$weibos->getUserWeibos();
    
   	    	$weibo = Wb_user_weibo::where(
   	    			function ($query) {
   	    				$query->where('status', '=', 0)->orWhere('status', '=', 3);
   	    			}
   	    		)->first();
   	    		
    	    if($weibo){
    	    	$weibo->status = 2;
    	   		$weibo->save();
  	
   		    	/**
  		    	 * 抓取com微博页面补全信息
   		    	 */    	
    			    
    	   		$weiboCom = new \App\Libraries\Contracts\CompleteWeiboInfo($weibo);
    			$weiboCom->Process();
    	
    		    var_dump($weibo);
    	    }
    }
}
