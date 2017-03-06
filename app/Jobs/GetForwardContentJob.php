<?php
/**
 * 设置抓取微博评论队列的队列
 */
namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Wb_forward_job;
use App\Libraries\Contracts\GetForward;


class GetForwardContentJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $wb_object_job;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Wb_forward_job $page)
    {
        //
        $this->wb_object_job = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	$getObjectJob = new GetForward($this->wb_object_job->mid,$this->wb_object_job->model);
    	$content = $getObjectJob->getHtml($this->wb_object_job->j_page);
    	$page_total = $getObjectJob->explainPage($content);
    	
    	//抓取完成后的状态
    	$this->wb_object_job->j_status = '2';
    	$this->wb_object_job->j_total = $page_total;
    	$this->wb_object_job->save();
    }
    
    /**
     * 处理失败任务
     *
     * @return void
     */
    public function failed()
    {
    	//失败任务的状态
    	$this->wb_object_job->j_status = '4';
    	$this->wb_object_job->save();
    }
    
}
