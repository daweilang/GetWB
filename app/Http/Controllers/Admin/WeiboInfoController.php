<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Weibo;

// use App\Jobs\GetWeiboJob;
use App\Jobs\GetWeiboAllJob;

use App\Libraries\Contracts\GetWeiboInfo;

/**
 * 抓取微博管理
 * @author daweilang
 * wb_status的几种状态， 0：设置，1：完成信息获取，2：重新设置，3：获取失败
 */

class WeiboInfoController extends Controller
{
	
	//设置组名
	public function __construct()
	{
		view()->share('groupName', 'weibo');
	}
	
    //
    public function index()
    {
    	return view('admin/weibo/weibo')->withWeibos(Weibo::paginate(10));
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
//     	$weibo->user_id = $request->user()->id; 
    
    	if ($weibo->save()) {
			//将任务添加到队列，获得微博信息
    		$job = (new GetWeiboAllJob($weibo))->delay(10);
//     		$job = (new GetWeiboJob($weibo))->onQueue('GetWeibo')->delay(10);
    		$this->dispatch($job);
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
    	$weibo->wb_status = '2'; //再次分析
    	 
    	if($weibo->save()){
    		//将任务添加到队列，获得微博信息
    		$job = (new GetWeiboAllJob($weibo))->delay(10);
    		//多进程时候使用队列命名
//     		$job = (new GetWeiboJob($weibo))->onQueue('GetWeibo')->delay(10);
    		$this->dispatch($job);
    		return redirect('admin/weibo');
    	}
    	else{
    		return redirect()->back()->withInput()->withErrors('更新失败');
    	}
    }
    
    
    public function destroy($id)
    {
//     	Weibo::find($id)->delete();
//     	return redirect()->back()->withInput()->withErrors('删除成功！');
    }
    
}
