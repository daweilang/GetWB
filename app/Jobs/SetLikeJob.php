<?php
/**
 * 抓取赞用户
 */
namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Libraries\Contracts\GetLike;
use App\Libraries\Classes\SetJobLog;

class SetLikeJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $mid;
    protected $job_log;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mid)
    {
        //
        $this->mid = $mid;
        $this->job_log = new SetJobLog();
        $this->job_log->createLog(['type'=>'like','object_id'=>$this->mid,'status'=>0]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	$likeJob = new GetLike($this->mid);
    	$likeJob->setJob();
    	$this->job_log->updateLog(['status'=>1]);
    }
    
    
    /**
     * 处理失败任务
     *
     * @return void
     */
    public function failed()
    {
    	//失败任务的状态
    	$this->job_log->updateLog(['status'=>2]);
    }
    
}
