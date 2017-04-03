<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Weibo;

use App\Libraries\Classes\SetJobLog;



/**
 * 任务管理页
 * @author daweilang
 * wb_status的几种状态， 0：设置，1：完成信息获取，2：重新设置，3：获取失败
 */

class JobLogsController extends Controller
{
	private $typeName = ['like' => '赞', 'comment' => '评论', 'forward' => '转发'];
	
	//执行延时
	public $delay = 1;
	
	//对了名称开关
	public $jobName = FALSE;
	
	
	/**
	 * 该条微博使用的model
	 * @var object
	 */
	private $model = 'Wb_user_weibo';
	
	
	//设置组名
	public function __construct()
	{
		view()->share('groupName', 'weibo');
		
		//获得全局延时时间设置
		if(empty($this->delay)){
			$this->delay = config('queue.delay');
		}
	}
	
	
	public function index($type, $mid)
	{
		$weibo = Weibo::where('mid', $mid)->first();
		
		//监控表
		$model = "\App\Models\Wb_".$type."_job";
		$dataLogs = $model::where('mid', $mid)->orderBy('j_id', 'desc')->orderBy('j_page','desc')->paginate(30);

		//数据表
		$model = "\App\Models\Wb_".$type;
		$dataCount = $model::where('mid', $mid)->count();
		
		$data = [ 	
				'weibo'=>$weibo, 
				'dataLogs'=>$dataLogs, 
				'dataCount'=>$dataCount, 
				'typeName'=> $this->typeName[$type],
				'type'=> $type,
		];
		
		return view('admin/joblogs/index', $data);
	}
	
    
    /**
     * 设置抓取任务，页面设置
     * @param unknown $type 类型
     * @param unknown $mid 微博id
     * @return 
     */
    public function settingJob($type, $mid)
    {
    	$job_log = new SetJobLog();
    	$job_log->createLog(['type'=>$type, 'object_id'=>$mid, 'status'=>0,]);
    	$thisModel = "\App\Libraries\Contracts\Get".ucfirst($type);
    	$likeJob = new $thisModel($mid);
    	$likeJob->setJob();
    	$job_log->updateLog(['status'=>1]);	
    	return redirect('admin/message/3/setJob');
    }
    
    
}
