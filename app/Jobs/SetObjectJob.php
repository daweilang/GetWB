<?php
/**
 * 抓取微博的转发、评论、和赞数据量巨大，采用队列模式
 * 队列模式只需job加载其对象，所以采用统一封装外层job
 * 具体单个实现，可见SetLikeJob等
 * by daweilang 2017-03-05
 */
namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Libraries\Classes\SetJobLog;

class SetObjectJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $mid;
    protected $job_log;
    protected $object;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mid, $object)
    {
        //
        $this->mid = $mid;
        $this->object = $object;
        $this->job_log = new SetJobLog();
        $this->job_log->createLog(['type'=>$this->object,'object_id'=>$mid,'status'=>0]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	$modelJob = "\App\Libraries\Contracts\Get".ucfirst($this->object);
    	$setThisJob = new $modelJob($this->mid);
    	$setThisJob->setJob();
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
