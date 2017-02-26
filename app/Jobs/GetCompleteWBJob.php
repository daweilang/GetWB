<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Libraries\Contracts\GetCompleteWB;
use App\Models\Wb_complete;
use App\Models\Wb_complete_job;

class GetCompleteWBJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $wb_complete_job;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Wb_complete_job $complete_page)
    {
    	//
    	$this->wb_complete_job = $complete_page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {		
    	//指定获取信息的model，setJob和getHtml需要该信息
    	$userinfo = Wb_complete::where('uid', $this->wb_complete_job->uid)->first();
    	
    	$getCompleteJob = new GetCompleteWB($userinfo);
    	$content = $getCompleteJob->getHtml($this->wb_complete_job->j_complete_page);	
    	$page_total = $getCompleteJob->explainPage($content);
    	
    	//抓取完成后的状态
    	$this->wb_complete_job->j_status = '2';
    	$this->wb_complete_job->j_complete_total = $page_total;
    	$this->wb_complete_job->save();
    }
    
    
    /**
     * 处理失败任务
     *
     * @return void
     */
    public function failed()
    {
    	//失败任务的状态
    	$this->wb_complete_job->j_status = '4';
    	$this->wb_complete_job->save();
    }
    
}
