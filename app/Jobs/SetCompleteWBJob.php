<?php
/**
 * 抓取赞用户
 */
namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Libraries\Contracts\GetCompleteWB;
use App\Libraries\Classes\SetJobLog;

use App\Models\Wb_complete;

class SetCompleteWBJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $uid;
    protected $userinfo;
    protected $job_log;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uid)
    {
        //
        $this->uid = $uid;
        $this->job_log = new SetJobLog();
        $this->job_log->createLog(['type'=>'weibo','object_id'=>$this->uid,'status'=>0]);
        
        //指定获取信息的model，setJob和getHtml需要该信息
        $this->userinfo = Wb_complete::where('uid', $this->uid)->first();
        ##任务开始时需要设置执行状态
        $this->userinfo->status = 3;
        $this->userinfo->save();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	$likeJob = new GetCompleteWB($this->userinfo);
    	$likeJob->setJob();
    	$this->job_log->updateLog(['status'=>1]);
    	
    	//setJob会递归获得设置下一页任务，只要最后一页时才能设置任务完成
		//$this->userinfo->status = 4;
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
    	$this->userinfo->status = -3;
    	$this->userinfo->save();
    }
    
}
