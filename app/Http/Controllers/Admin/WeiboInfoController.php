<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Weibo;

use App\Jobs\GetWeiboJob;


/**
 * 抓取单条微博管理
 * @author daweilang
 * wb_status的几种状态， 0：设置，1：完成信息获取，2：重新设置，3：获取失败
 */

class WeiboInfoController extends Controller
{
	
	//执行延时
	public $delay = 1;
	
	//对了名称开关
	public $jobName = FALSE;
	
	//设置组名
	public function __construct()
	{
		view()->share('groupName', 'weibo');
		view()->share('path', 'admin/weibo');
		
		//获得全局延时时间设置
		if(empty($this->delay)){
			$this->delay = config('queue.delay');
		}
	}
	
    //
    public function index(Request $request)
    {
    	if ($mid = $request->input('mid')){
    		$weibos = Weibo::where('mid', $mid)->orderBy('id', 'desc')->paginate(20);
    	}
    	else{
    		$weibos = Weibo::orderBy('id', 'desc')->paginate(20);
    	}   	
    	return view('admin/weibo/weibo', ['weibos'=>$weibos, 'mid'=>$mid]);
    }
    
    
    public function create()
    {
    	return view('admin/weibo/create');
    }
    
    
    public function store(Request $request)
    {
    	// 数据验证
    	$this->validate($request, [
    			'wb_name' => 'required|max:255', 
    			'wb_url' => 'required', 
    	]);
    
    	$weibo = new Weibo(); 
    	$weibo->wb_name = $request->get('wb_name'); 
    	$weibo->wb_url = $request->get('wb_url'); 
    	$weibo->wb_scope = json_encode($request->get('wb_scope'));
    	
    	if ($weibo->save()) {
    		$this->setThisJob($weibo);
    		return redirect('admin/weibo');
    	} 
    	else {
    		// 保存失败，跳回来路页面，保留用户的输入，并给出提示
    		return redirect()->back()->withInput()->withErrors('保存失败！');
    	}
    }
    
    
    public function edit($id)
    {
    	return view('admin/weibo/edit')->withWeibo(Weibo::find($id));
    }
 
    
    public function update(Request $request, $id)
    {
    	$this->validate($request, [
    			'wb_name' => 'required|max:255',
    			'wb_url' => 'required',
    	]);
    	
    	$weibo = Weibo::find($id);
    	$weibo->wb_name = $request->get('wb_name');
    	$weibo->wb_url = $request->get('wb_url');
    	$weibo->wb_scope = json_encode($request->get('wb_scope'));
    	$weibo->status = '2'; //再次分析
    	 
    	if($weibo->save()){
    		$this->setThisJob($weibo);
    		return redirect('admin/weibo');
    	}
    	else{
    		return redirect()->back()->withInput()->withErrors('更新失败');
    	}
    }
    
    
    /**
     * 获得微博信息
     * @param unknown $weibo
     */
    private function setThisJob($weibo)
    {	
    	//将任务添加到队列，获得微博信息
    	if($this->jobName){
    		$job = (new GetWeiboJob($weibo))->onQueue('GetWeibo')->delay($this->delay);
    	}
    	else{
    		$job = (new GetWeiboJob($weibo))->delay($this->delay);
    	}
    	$this->dispatch($job);
    }
    
    
    public function exampleTest($mid){
    	 
    	//     	$getUserInfo = new \App\Libraries\Contracts\GetForward("4078059135268877");
    	//     	$getUserInfo->explainPage($getUserInfo->getHtml(1));
    	//     	$getUserInfo->explainPage("", "wbHtml/$getUserInfo->mid/like_1139");
    
    	//     	$getUserInfo = new \App\Libraries\Contracts\GetComment("4030488252993648");
    	//     	$getUserInfo->explainPage($getUserInfo->getHtml(643));
    	//     	$getUserInfo->explainPage("", "wbHtml/$getUserInfo->mid/like_1139");
    
    	//     	$getUserInfo = new \App\Libraries\Contracts\GetLike("4030488252993648");
    	//     	$getUserInfo->explainPage($getUserInfo->getHtml(1144));
    	//     	$getUserInfo->explainPage("", "wbHtml/$getUserInfo->mid/like_1139");
    
    	//     	$weibo = Weibo::where('mid', "4078059135268877")->first();
    	//     	$getUserInfo = new \App\Libraries\Contracts\GetWeiboInfo($weibo);
    	//     	$getUserInfo->explainWeibo($getUserInfo->getWeiboHtml());
    	//     	$getUserInfo->explainPage("", "wbHtml/$getUserInfo->mid/like_1139");
    
    }
    
}
