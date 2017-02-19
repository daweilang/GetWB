<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Weibo;
use App\Models\Wb_comment_job;
use App\Jobs\SetCommentJob;
use App\Libraries\Contracts\GetComment;

// use App\Jobs\GetWeiboCommentJob;


/**
 * 评论队列任务管理表
 * @author daweilang
 * wb_status的几种状态， 0：设置，1：完成信息获取，2：重新设置，3：获取失败
 */

class CommentJobController extends Controller
{
	
	//设置组名
	public function __construct()
	{
		view()->share('groupName', 'comment');
	}
	
    //
    public function index($mid)
    {
    	$weibo = Weibo::where('mid', $mid)->first();
    	$data = Wb_comment_job::where('mid', $mid)->paginate(15);
    	return view('admin/comment/comment_job', ['weibo'=>$weibo, 'comments'=>$data]);
    }
    
    /**
     * 设置获取微博评论的任务，页面设置
     * @param unknown $mid 微博id
     * @return 
     */
    public function Setting($mid)
    {  	
    	$commnetJob = new GetComment($mid);
    	$commnetJob->setCommentJob();
    	return redirect('admin/message/1/setCommentJob');
    }

    public function settingJob($mid)
    {
    	//微博评论页太多时，采用队列模式
    	$job = (new SetCommentJob($mid))->delay(10);
    	//多进程时候使用命名
//     	$job = (new SetCommentJob($mid))->onQueue('SetComment')->delay(10);
    	$this->dispatch($job);
    	return redirect('admin/message/3/setCommentJobJob');
    }
    
    
}
