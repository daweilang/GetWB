<?php

namespace App\Jobs;

use Log;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Weibo;
use App\Libraries\Contracts\GetWeiboInfo;

class GetWeiboAllJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $weibo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Weibo $weibo)
    {
        //
        $this->weibo = $weibo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	//只有微博状态为未分析时候才有效
    	if(in_array($this->weibo->wb_status, [0, 2])){
    		
    		###分析微博页的业务逻辑
	    	$getContent = new GetWeiboInfo($this->weibo);	    	
	    	//获得微博页面内容
	    	$getContent->getWeiboHtml();
	    	//分析微博的评论
	    	$getContent->explainWeiboComment($this->weibo);
	    	
	    	//分析评论的业务逻辑采用队列模式
	    	$job = (new SetCommentJob($getContent->gid))->delay(10);
	    	//多进程时候使用命名
	    	//$job = (new SetCommentJob($gid))->onQueue('SetComment')->delay(10);
	    	dispatch($job);
	    	
   			return true; 	
    	}
    	else{
    		return ;	
    	}
    }
    
    /**
     * 处理失败任务
     *
     * @return void
     */
    public function failed()
    {
    	Log::info('无法获得微博信息');
    }
}
