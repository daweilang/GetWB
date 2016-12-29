<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Weibo;
use App\Libraries\Contracts\GetWeiboInfo;

class GetWeiboJob extends Job implements ShouldQueue
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
	    	$getContent = new GetWeiboInfo($this->weibo);	    	
	    	//获得微博页面内容
	    	$getContent->getWeiboHtml();
	    	//分析微博的评论
	    	$getContent->explainWeiboComment($this->weibo);
   			return true; 	
    	}
    	else{
    		return ;	
    	}
    }
    
}
