<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
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
    public function index($gid)
    {
    	$weibo = Weibo::where('wb_comment_gid', $gid)->first();
    	$data = Wb_comment_job::where('gid', $gid)->paginate(15);
    	return view('admin/comment/comment_job', ['weibo'=>$weibo, 'comments'=>$data]);
    }
    
    /**
     * 设置获取微博评论的任务，页面设置
     * @param unknown $gid 微博评论组id
     * @return 
     */
    public function Setting($gid)
    {  	
    	$commnetJob = new GetComment($gid);
    	$commnetJob->setCommentJob();
    	return redirect('admin/message/1/setCommentJob');
    }

    public function settingJob($gid)
    {
    	//微博评论页太多时，采用队列模式
    	$job = (new SetCommentJob($gid))->delay(10);
    	//多进程时候使用命名
//     	$job = (new SetCommentJob($gid))->onQueue('SetComment')->delay(10);
    	$this->dispatch($job);
    	return redirect('admin/message/3/setCommentJobJob');
    }
    
    
}
