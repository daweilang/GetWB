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
    
    //执行延时
    public $delay;
    
    //任务名，true或false
    private $jobName = FALSE;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Weibo $weibo)
    {
        //
        $this->weibo = $weibo;
        
        //获得全局延时时间设置
        if(empty($this->delay)){
        	$this->delay = config('queue.delay');
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	//只有微博状态为未分析时候才有效
    	if(in_array($this->weibo->status, [0, 2])){
    		
    		###分析微博页的业务逻辑
	    	$getContent = new GetWeiboInfo($this->weibo);	    	
	    	//获得微博页面内容
	    	$content = $getContent->getWeiboHtml();
	    	//分析微博的评论
	    	$getContent->explainWeibo($content);

	    	//分析微博赞的业务逻辑采用队列模式
	    	if($this->jobName){
	    		//多进程时候使用命名
	    		$job = (new SetLikeJob($getContent->mid))->onQueue('SetLike')->delay($this->delay);
	    	}
	    	else{
	    		$job = (new SetLikeJob($getContent->mid))->delay($this->delay);
	    	}
	    	dispatch($job);
	    	
	    	//分析评论的业务逻辑采用队列模式
// 	    	if($this->jobName){
// 	    	//多进程时候使用命名
// 	    		$job = (new SetCommentJob($getContent->mid))->onQueue('SetComment')->delay($this->delay);
// 	    	}
// 	    	else{
// 	    		$job = (new SetCommentJob($getContent->mid))->delay($this->delay);
// 	    	}
// 	    	dispatch($job);

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
