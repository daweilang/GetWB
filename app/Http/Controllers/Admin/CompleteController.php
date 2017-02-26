<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Libraries\Classes\TraitGetConfig;
use App\Models\Wb_complete;
use App\Models\Wb_user_weibo;

use App\Jobs\GetUserCompleteJob;
use App\Jobs\SetCompleteWBJob;


/**
 * 系统完整的获得微博用户数据，从用户微博首页开始，抓取所有微博，
 * 获得微博的评论、赞，
 * 以微博的评论和赞数据为基础，交叉比对用户是否关注微博用户，以得到的微博用的活跃粉丝
 * @author daweilang
 * status的几种状态， 获取用户信息状态： 0,未获取；1，已抓取；2，重新抓取；3，设置抓取全部微博任务；4，任务设置完成；-1，抓取用户信息失败；-3，设置抓取微博任务失败
 */

class CompleteController extends Controller
{
	/**
	 * 自定义trait，获得需要的配置
	 */
	use TraitGetConfig;
	
	//队列名称开关
	public $jobName = FALSE;
	
	/**
	 * 设置队列延时时间设置
	 * 默认由TraitGetConfig设置
	 * @var integer
	 */
	public $delay = 3;
	
	public function __construct()
	{
		//设置组名
		view()->share('groupName', 'complete');		
		view()->share('routeName', 'index');
		view()->share('path', 'admin/complete');
		
		//获得队列任务配置
		$this->getQueueConf();
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
    	$this->setThisJob($usercard);
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
			$this->setThisJob($weibo->usercard);
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
    	
    	$weibos = Wb_user_weibo::where("uid",$userinfo['uid'])->paginate(30);
    	$count = Wb_user_weibo::where("uid",$userinfo['uid'])->count();
    	
    	return view('admin/complete/weibos', ['userinfo' => $userinfo, 'weibos' => $weibos, 'count'=>$count]);
    }
    
    
    /**
     * 设置获取全部微博任务，设置job任务
     * 接口 http://weibo.com/p/aj/v6/mblog/mbloglist?ajwvr=6&page=%d&domain=%d&id=%d
     * @param unknown $uid 用户id
     * @return
     */
    public function settingWB($uid)
    {
    	if($this->jobName){
    		//多进程时候使用命名
    		$job = (new SetCompleteWBJob($uid))->onQueue('GetCompleteWBJob')->delay($this->delay);
    	}
    	else{
    		$job = (new SetCompleteWBJob($uid))->delay($this->delay);
	    	
    	}
    	$this->dispatch($job);
    	return redirect('admin/message/3/setCommentJobJob');
    }
    
    
    /**
     * 获得用户信息
     * @param unknown $usercard
     */
    private function setThisJob($usercard)
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
    
    
    
    public function exampleTest($uid){
    	
//     	$getUserInfo = new \App\Libraries\Contracts\GetUserComplete('dailyjapan');
//     	$getUserInfo->explainUserWeibo($getUserInfo->getUserHtml());
		$userinfo = Wb_complete::where('uid', '1778181861')->first();
    	$getUserInfo = new \App\Libraries\Contracts\GetCompleteWb($userinfo);
//     	$getUserInfo->explainPage($getUserInfo->getHtml(38));
    	$getUserInfo->explainPage("", "wbUserHtml/1778181861/weibos_637");
    
    }
}
