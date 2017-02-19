<?php
/**
 * 由于微博评论页过多，设置抓取评论任务队列时间较长
 * 设计设置抓取微博评论队列的队列
 * 队列的任务是根据评论页数设置抓取队列
 */
namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Libraries\Contracts\GetComment;
use App\Libraries\Classes\SetJobLog;

class SetCommentJob extends Job implements ShouldQueue
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
        $this->job_log->createLog(['type'=>'comment','object_id'=>$mid,'status'=>0]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	$commentJob = new GetComment($this->mid);
    	$commentJob = $commentJob->setJob();
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
