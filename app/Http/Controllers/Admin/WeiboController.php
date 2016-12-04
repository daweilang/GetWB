<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Weibo;


use App\Libraries\Contracts\GetWeiboContent;
use App\Jobs\GetWeiboInfo;
// use App\Jobs\MyJob;
// use Illuminate\Queue;

/**
 * 抓取微博管理
 * @author daweilang
 *
 */

class WeiboController extends Controller
{
	
	//设置组名
	public function __construct()
	{
		view()->share('groupName', 'weibo');
	}
	
    //
    public function index()
    {
    	return view('admin/weibo/index')->withWeibos(Weibo::all());
    }
    
    public function create()
    {
    	return view('admin/weibo/create');
    }
    
    public function store(Request $request) // Laravel 的依赖注入系统会自动初始化我们需要的 Request 类
    {
    	// 数据验证
    	$this->validate($request, [
    			'wb_title' => 'required|unique:weibos|max:255', // 必填、在 articles 表中唯一、最大长度 255
    			'wb_url' => 'required', // 必填
    	]);
    
    	$weibo = new Weibo(); 
    	$weibo->wb_title = $request->get('wb_title'); // 将 POST 提交过了的 title 字段的值赋给 weibo 的 title 属性
    	$weibo->wb_url = $request->get('wb_url'); // 同上
//     	$weibo->user_id = $request->user()->id; 
    
    	if ($weibo->save()) {
			//将任务添加到队列，获得微博信息
			//http://weibo.com/1563926367/EcN8BcyME?type=comment#_rnd1477219631405
    		$job = (new GetWeiboInfo($weibo))->delay(10);
    		$this->dispatch($job);
    		return redirect('admin/weibo'); // 保存成功，跳转到 文章管理 页
    	} 
    	else {
    		// 保存失败，跳回来路页面，保留用户的输入，并给出提示
    		return redirect()->back()->withInput()->withErrors('保存失败！');
    	}
    }
    
    /**
     * 发送提醒邮件到指定用户
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function getWeiboCookie(Request $request, $id)
    {       
    	$weibo = Weibo::findOrFail($id);
    	$job = (new GetWeiboInfo($weibo))->delay(10);
    	$this->dispatch($job);
    	return "已设置任务";
    }
    
    
    public function edit($id)
    {
    	return view('admin/weibo/edit')->withWeibo(Weibo::find($id));
    }
 
    public function update(Request $request, $id)
    {
    	$this->validate($request, [
    			'wb_title' => 'required|unique:weibos,wb_title,'.$id.'|max:255',
    			'wb_url' => 'required',
    	]);
    	$weibo = Weibo::find($id);
    	$weibo->wb_title = $request->get('wb_title');
    	$weibo->wb_url = $request->get('wb_url');
    	 
    	if($weibo->save()){
    		//将任务添加到队列，获得微博信息
    		//http://weibo.com/1563926367/EcN8BcyME?type=comment#_rnd1477219631405
    		$job = (new GetWeiboInfo($weibo))->delay(10);
    		$this->dispatch($job);
    		return redirect('admin/weibo');
    	}
    	else{
    		return redirect()->back()->withInput()->withErrors('更新失败');
    	}
    }
    
    
    public function destroy($id)
    {
    	Article::find($id)->delete();
    	return redirect()->back()->withInput()->withErrors('删除成功！');
    }
    
    
    public function getWeiboHtml()
    {
    	$getContent = new GetWeiboContent();
    	$url = "http://weibo.com/1563926367/EcN8BcyME?type=comment#_rnd1477219631405";
    	$content = $getContent->getWeiboHtml($url);
    	return $content;
    }
}
